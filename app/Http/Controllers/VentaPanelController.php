<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Adler\Venta      as AdlerVenta;
use App\Models\Adler\Shipment   as AdlerShipment;
use App\Models\Amigurumis\Venta as AmiguVenta;
use App\Models\Amigurumis\Shipment as AmiguShipment;

class VentaPanelController extends Controller   
{
    /** Vista principal del panel */
    public function index()
    {
        return view('panel.index');
    }

    /** Helper para elegir modelo de venta según origen */
    protected function ventaModel(string $origen)
    {
        return $origen === 'adler' ? AdlerVenta::class : AmiguVenta::class;
    }

    /** Helper para elegir modelo de shipment según origen */
    protected function shipmentModel(string $origen)
    {
        return $origen === 'adler' ? AdlerShipment::class : AmiguShipment::class;
    }

    /** Data unificada para la tabla principal (ventas abiertas) */
    public function data(Request $req)
    {
        $q = trim((string) $req->get('q'));

        $adler = AdlerVenta::query()
            ->with('latestShipment')
            ->abiertas()
            ->when($q, function ($qq) use ($q) {
                $qq->where('codigo','like',"%$q%")
                   ->orWhere('email','like',"%$q%");
            })
            ->orderByDesc('fecha_hora')
            ->get()
            ->map(function ($v) {
                return [
                    'origen'       => 'adler',
                    'id'           => $v->idventa,
                    'codigo'       => $v->codigo,
                    'cliente'      => trim(($v->nombre ?? '').' '.($v->apellido ?? '')),
                    'email'        => $v->email,
                    'subtotal'     => (float)($v->subtotal ?? 0),
                    'cargo_envio'  => (float)($v->cargo_envio ?? 0),
                    'total_venta'  => (float)($v->total_venta ?? 0),
                    'payment_status'     => $v->payment_status ?? 'pending',
                    'fulfillment_status' => $v->fulfillment_status ?? 'pending',
                    'fulfillment_method' => $v->fulfillment_method ?? null,
                    'fecha_hora'   => optional($v->fecha_hora)->toDateTimeString(),
                    'shipment_status' => optional($v->latestShipment)->status,
                ];
            });

        $amigu = AmiguVenta::query()
            ->with('latestShipment')
            ->abiertas()
            ->when($q, function ($qq) use ($q) {
                $qq->where('codigo','like',"%$q%")
                   ->orWhere('email','like',"%$q%");
            })
            ->orderByDesc('fecha_hora')
            ->get()
            ->map(function ($v) {
                return [
                    'origen'       => 'amigurumis',
                    'id'           => $v->idventa,
                    'codigo'       => $v->codigo,
                    'cliente'      => trim(($v->nombre ?? '').' '.($v->apellido ?? '')),
                    'email'        => $v->email,
                    'subtotal'     => (float)($v->subtotal ?? 0),
                    'cargo_envio'  => (float)($v->cargo_envio ?? 0),
                    'total_venta'  => (float)($v->total_venta ?? 0),
                    'payment_status'     => $v->payment_status ?? 'pending',
                    'fulfillment_status' => $v->fulfillment_status ?? 'pending',
                    'fulfillment_method' => $v->fulfillment_method ?? null,
                    'fecha_hora'   => optional($v->fecha_hora)->toDateTimeString(),
                    'shipment_status' => optional($v->latestShipment)->status,
                ];
            });

        $ventas = $adler
            ->concat($amigu)
            ->sortByDesc('fecha_hora')
            ->values()
            ->all();

        return response()->json($ventas);
    }

    /** Detalle completo de una venta (venta + paquetes + shipment + logs) */
    public function show(string $origen, int $id)
    {
        $ventaModel = $this->ventaModel($origen);

        /** @var \App\Models\Common\VentaBase $venta */
        $venta = $ventaModel::with(['lineas.articulo','shipments','statusLogs'])
            ->findOrFail($id);

        // Normalizar detalle
        $detalle = collect($venta->lineas ?? [])
            ->map(function ($d) {
                return [
                    'iddetalle'  => $d->iddetalle,
                    'idsub'      => $d->idsub,
                    'idarticulo' => $d->idarticulo,
                    'qty'        => (int)($d->qty ?? 1),
                    'precio'     => (float)($d->precio ?? 0),
                    'subtotal'   => (float)($d->subtotal ?? 0),
                    'articulo'   => [
                        'name'  => optional($d->articulo)->name,
                        'image' => optional($d->articulo)->image,
                    ],
                ];
            })
            ->values()
            ->all();

        // Agrupar por paquete (idsub)
        $grupos = collect($detalle)
            ->groupBy('idsub')
            ->map(function ($items, $idsub) {
                return [
                    'idsub' => (int) $idsub,
                    'total' => collect($items)->sum('subtotal'),
                    'items' => array_values($items->all()),
                ];
            })
            ->values()
            ->all();

        $shipment = $venta->latestShipment ?: $venta->shipments->first();

        $logs = $venta->statusLogs()
            ->orderByDesc('occurred_at')
            ->limit(50)
            ->get(['domain','from_status','to_status','note','occurred_at','meta']);

        return response()->json([
            'origen' => $origen,
            'venta' => [
                'id'           => $venta->idventa,
                'codigo'       => $venta->codigo,
                'email'        => $venta->email,
                'tipo'         => $venta->tipo,
                'subtotal'     => $venta->subtotal,
                'cargo_envio'  => $venta->cargo_envio,
                'total_venta'  => $venta->total_venta,
                'fecha_hora'   => optional($venta->fecha_hora)->toDateTimeString(),
                'nombre'       => $venta->nombre,
                'apellido'     => $venta->apellido,
                'domicilio'    => $venta->domicilio,
                'distrito'     => $venta->distrito,
                'provincia'    => $venta->provincia,
                'departamento' => $venta->departamento,
                'dni'          => $venta->dni,
                'referencia'   => $venta->referencia,
                'celular'      => $venta->celular,
                'payment_status'     => $venta->payment_status,
                'fulfillment_status' => $venta->fulfillment_status,
                'fulfillment_method' => $venta->fulfillment_method,
                'paid_at'      => optional($venta->paid_at)->toDateTimeString(),
                'ready_at'     => optional($venta->ready_at)->toDateTimeString(),
                'shipped_at'   => optional($venta->shipped_at)->toDateTimeString(),
                'delivered_at' => optional($venta->delivered_at)->toDateTimeString(),
                'cancelled_at' => optional($venta->cancelled_at)->toDateTimeString(),
                'detalle'      => $detalle,
            ],
            'shipment' => $shipment ? [
                'id'              => $shipment->id,
                'venta_id'        => $shipment->venta_id,
                'status'          => $shipment->status,
                'carrier'         => $shipment->carrier,
                'service'         => $shipment->service,
                'tracking_number' => $shipment->tracking_number,
                'tracking_url'    => $shipment->tracking_url,
                'shipping_cost'   => $shipment->shipping_cost,
                'weight_kg'       => $shipment->weight_kg,
                'address_to'      => $shipment->address_to,
                'shipped_at'      => optional($shipment->shipped_at)->toDateTimeString(),
                'delivered_at'    => optional($shipment->delivered_at)->toDateTimeString(),
                'created_at'      => optional($shipment->created_at)->toDateTimeString(),
            ] : null,
            'grupos' => $grupos,
            'logs'   => $logs,
        ]);
    }

    /** Actualiza datos básicos de la venta (nombre, dirección, payment_status, etc.) */
    public function updateVenta(Request $req, string $origen, int $id)
    {
        $ventaModel = $this->ventaModel($origen);

        /** @var \App\Models\Common\VentaBase $venta */
        $venta = $ventaModel::findOrFail($id);

        $data = $req->validate([
            'nombre'       => ['nullable','string','max:120'],
            'apellido'     => ['nullable','string','max:120'],
            'email'        => ['nullable','email','max:180'],
            'celular'      => ['nullable','string','max:30'],
            'domicilio'    => ['nullable','string','max:255'],
            'distrito'     => ['nullable','string','max:120'],
            'provincia'    => ['nullable','string','max:120'],
            'departamento' => ['nullable','string','max:120'],
            'referencia'   => ['nullable','string','max:255'],
            'payment_status'     => ['nullable','string','max:50'],
            'fulfillment_method' => ['nullable','string','max:50'],
        ]);

        $venta->fill($data);
        $venta->save();

        return response()->json([
            'ok'    => true,
            'venta' => $venta->only([
                'idventa','nombre','apellido','email','celular',
                'domicilio','distrito','provincia','departamento',
                'referencia','payment_status','fulfillment_method',
            ]),
        ]);
    }

    /** Cambia fulfillment_status usando transitionFulfillment */
    public function updateFulfillment(Request $req, string $origen, int $id)
    {
        $ventaModel = $this->ventaModel($origen);

        /** @var \App\Models\Common\VentaBase $venta */
        $venta = $ventaModel::findOrFail($id);

        $data = $req->validate([
            'to'   => ['required','string','max:50'],
            'note' => ['nullable','string','max:300'],
        ]);

        $to   = $data['to'];
        $note = $data['note'] ?? null;

        $venta->transitionFulfillment($to, null, $note);

        return response()->json([
            'ok'    => true,
            'venta' => [
                'idventa'            => $venta->idventa,
                'fulfillment_status' => $venta->fulfillment_status,
                'ready_at'           => optional($venta->ready_at)->toDateTimeString(),
                'shipped_at'         => optional($venta->shipped_at)->toDateTimeString(),
                'delivered_at'       => optional($venta->delivered_at)->toDateTimeString(),
                'cancelled_at'       => optional($venta->cancelled_at)->toDateTimeString(),
            ],
        ]);
    }

    /** Actualiza meta de shipment (carrier, tracking, cost, weight...) */
    public function updateShipmentMeta(Request $req, string $origen, int $id)
    {
        $shipmentModel = $this->shipmentModel($origen);

        /** @var \App\Models\Common\ShipmentBase $s */
        $s = $shipmentModel::findOrFail($id);

        $data = $req->validate([
            'carrier'         => ['nullable','string','max:60'],
            'service'         => ['nullable','string','max:80'],
            'tracking_number' => ['nullable','string','max:80'],
            'tracking_url'    => ['nullable','url','max:255'],
            'shipping_cost'   => ['nullable','numeric','min:0'],
            'weight_kg'       => ['nullable','numeric','min:0'],
        ]);

        foreach ($data as $field => $value) {
            $s->{$field} = $value;
        }

        $s->save();

        return response()->json([
            'ok'       => true,
            'shipment' => $s->only([
                'id','carrier','service','tracking_number','tracking_url',
                'shipping_cost','weight_kg','status'
            ]),
        ]);
    }

    /** Cambia status del shipment y sincroniza venta + log */
    public function updateShipmentStatus(Request $req, string $origen, int $id)
    {
        $shipmentModel = $this->shipmentModel($origen);

        /** @var \App\Models\Common\ShipmentBase $envio */
        $envio = $shipmentModel::findOrFail($id);

        $data = $req->validate([
            'status' => [
                'required',
                Rule::in([
                    'label_created','in_transit','out_for_delivery',
                    'delivered','failed','returned','pickup_ready',
                ]),
            ],
            'note'   => ['nullable','string','max:300'],
        ]);

        $allowed = [
            'label_created'    => ['in_transit','failed','returned'],
            'in_transit'       => ['out_for_delivery','delivered','failed','returned'],
            'out_for_delivery' => ['delivered','failed','returned'],
            'pickup_ready'     => ['delivered'],
            'delivered'        => [],
            'failed'           => [],
            'returned'         => [],
        ];

        $from = $envio->status;
        $to   = $data['status'];

        if (!in_array($to, $allowed[$from] ?? [], true)) {
            return response()->json([
                'error' => "Transición no permitida: $from → $to",
            ], 422);
        }

        $envio->status = $to;
        if ($to === 'in_transit' && !$envio->shipped_at)   $envio->shipped_at   = now();
        if ($to === 'delivered'  && !$envio->delivered_at) $envio->delivered_at = now();
        $envio->save();

        $venta = $envio->venta;

        if ($venta && $to === 'delivered') {
            $venta->fulfillment_status = 'delivered';
            if (empty($venta->delivered_at)) {
                $venta->delivered_at = now();
            }
            $venta->save();
        }

        if ($venta && method_exists($venta, 'statusLogs')) {
            $venta->statusLogs()->create([
                'domain'      => 'shipment',
                'from_status' => $from,
                'to_status'   => $to,
                'actor_type'  => 'panel',
                'actor_id'    => null,
                'note'        => $data['note'] ?? null,
                'meta'        => ['shipment_id' => $envio->id],
                'occurred_at' => now(),
            ]);
        }

        return response()->json([
            'ok'     => true,
            'status' => $envio->status,
        ]);
    }
}
