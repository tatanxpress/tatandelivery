<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarritoExtraModelo extends Model
{
    protected $table = 'carrito_extra';    
    public $timestamps = false;
    protected $fillable = [
        'carrito_temporal_id', 'producto_id', 'nota_producto', 'cantidad'
    ];    

}
