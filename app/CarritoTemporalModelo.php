<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarritoTemporalModelo extends Model
{
    protected $table = 'carrito_temporal';    
    public $timestamps = false;
    protected $fillable = [
        'users_id', 'servicios_id', 'zonas_id'
    ];
}