<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class SearchBase extends Model
{
    protected $table = 'searches';

    protected $fillable = [
        'idpersona','tipo','name','volumen','codigo','stock','categoria','image','thumb',
        'precio','costo','preciof','description','caracteristicas','caracteristicas2',
        'especificaciones','puntos','image1','image2','image3','impropio',
        'soli','fecha','preventa','preventab','oferta'
    ];

    protected $casts = [
        'caracteristicas2' => 'array',
        'especificaciones' => 'array',
        'precio'   => 'decimal:2',
        'costo'    => 'decimal:2',
        'preciof'  => 'decimal:2',
        'preventa' => 'decimal:2',
    ];
}
