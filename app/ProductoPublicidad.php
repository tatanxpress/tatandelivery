<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoPublicidad extends Model
{
    protected $table = 'publicidad_producto';
    public $timestamps = false;

    protected $fillable = [
        'publicidad_id', 'producto_id'
    ];
}
