<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaBase extends EloquentModel
{
    protected $table = 'venta';
    protected $primaryKey = 'idventa';
    public $timestamps = true;

    protected $fillable = [
        'iduser','codigo','subtotal','total_venta','tipo','cargo_envio','detalle','fecha_hora',
        'nombre','email','user-mail','apellido','domicilio','celular','distrito','provincia',
        'departamento','dni','referencia',
        'payment_status','fulfillment_status','fulfillment_method',
        'paid_at','ready_at','shipped_at','delivered_at','cancelled_at',
    ];

    protected $casts = [
        'fecha_hora'   => 'datetime',
        'paid_at'      => 'datetime',
        'ready_at'     => 'datetime',
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function lineas()
    {
        return $this->hasMany(DetalleVentaBase::class, 'idventa', 'idventa');
    }

    public function shipments()
    {
        return $this->hasMany(ShipmentBase::class, 'venta_id', 'idventa');
    }

    public function latestShipment()
    {
        return $this->hasOne(ShipmentBase::class, 'venta_id', 'idventa')->latestOfMany();
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLogBase::class, 'venta_id', 'idventa');
    }

    public function scopeAbiertas($query)
    {
        // No entregadas ni canceladas ni devueltas
        return $query->whereNotIn('fulfillment_status', ['delivered','cancelled','returned']);
    }

    public function transitionFulfillment(string $to, ?EloquentModel $actor = null, ?string $note = null): void
    {
        $allowed = [
            'pending'          => ['ready_for_pickup','ready_to_ship','cancelled'],
            'ready_for_pickup' => ['delivered','cancelled'],
            'ready_to_ship'    => ['in_transit','cancelled'],
            'in_transit'       => ['delivered','returned','cancelled'],
            'delivered'        => [],
            'cancelled'        => [],
            'returned'         => [],
        ];

        $from = $this->fulfillment_status ?? 'pending';
        if (!in_array($to, $allowed[$from] ?? [], true)) {
            throw new \DomainException("Transición no permitida: $from → $to");
        }

        DB::transaction(function () use ($from, $to, $actor, $note) {
            $this->fulfillment_status = $to;

            if ($to === 'ready_for_pickup' || $to === 'ready_to_ship') {
                $this->ready_at = now();
            }
            if ($to === 'in_transit') {
                $this->shipped_at = now();
            }
            if ($to === 'delivered') {
                $this->delivered_at = now();
            }
            if ($to === 'cancelled') {
                $this->cancelled_at = now();
            }

            $this->save();

            if ($to === 'ready_for_pickup') {
                $this->ensureShipmentForPickup();
            }

            if ($to === 'ready_to_ship') {
                $this->ensureShipment();
            }

            try {
                $this->statusLogs()->create([
                    'domain'      => 'fulfillment',
                    'from_status' => $from,
                    'to_status'   => $to,
                    'actor_type'  => $actor?->getMorphClass(),
                    'actor_id'    => $actor?->getKey(),
                    'note'        => $note,
                    'occurred_at' => now(),
                    'meta'        => null,
                ]);
            } catch (\Throwable $e) {
                Log::warning('No se pudo guardar status log', [
                    'venta' => $this->idventa,
                    'err'   => $e->getMessage(),
                ]);
            }
        });
    }

    public function ensureShipment(): ShipmentBase
    {
        if ($ex = $this->shipments()->first()) {
            return $ex;
        }

        $addr = [
            'address'      => $this->domicilio,
            'distrito'     => $this->distrito,
            'provincia'    => $this->provincia,
            'departamento' => $this->departamento,
            'name'         => trim(($this->nombre ?? '').' '.($this->apellido ?? '')),
            'email'        => $this->email,
            'dni'          => $this->dni,
            'phone'        => $this->celular,
        ];

        return $this->shipments()->create([
            'status'          => 'label_created',
            'carrier'         => null,
            'service'         => null,
            'tracking_number' => null,
            'tracking_url'    => null,
            'shipping_cost'   => $this->cargo_envio ?? 0,
            'weight_kg'       => null,
            'address_to'      => $addr,
            'shipped_at'      => null,
            'delivered_at'    => null,
        ]);
    }

    public function ensureShipmentForPickup(): ShipmentBase
    {
        if ($ex = $this->shipments()->whereIn('status', ['pickup_ready','delivered'])->first()) {
            return $ex;
        }

        $addr = [
            'address'      => $this->domicilio,
            'distrito'     => $this->distrito,
            'provincia'    => $this->provincia,
            'departamento' => $this->departamento,
            'name'         => trim(($this->nombre ?? '').' '.($this->apellido ?? '')),
            'email'        => $this->email,
            'dni'          => $this->dni,
            'phone'        => $this->celular,
        ];

        return $this->shipments()->create([
            'status'          => 'pickup_ready',
            'carrier'         => null,
            'service'         => 'recojo',
            'tracking_number' => null,
            'tracking_url'    => null,
            'shipping_cost'   => 0,
            'weight_kg'       => null,
            'address_to'      => $addr,
            'shipped_at'      => null,
            'delivered_at'    => null,
        ]);
    }
}
