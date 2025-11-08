<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class ShipmentBase extends Model
{
    protected $table = 'shipments';

    protected $fillable = [
        'venta_id','status','carrier','service','tracking_number','tracking_url',
        'shipping_cost','weight_kg','address_to','shipped_at','delivered_at',
    ];

    protected $casts = [
        'address_to' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(VentaBase::class, 'venta_id', 'idventa');
    }
}
