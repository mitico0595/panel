<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaBase extends Model
{
    protected $table = 'detalle_venta';
    protected $primaryKey = 'iddetalle';
    public $timestamps = false;

    protected $fillable = [
        'idventa','idsub','idarticulo','qty','precio','subtotal','opinion','valoracion',
    ];

    public function venta()
    {
        return $this->belongsTo(VentaBase::class, 'idventa', 'idventa');
    }

    public function articulo()
    {
        return $this->belongsTo(SearchBase::class, 'idarticulo', 'id');
    }
}
