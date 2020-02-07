<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ordenes extends Model
{
    protected $table = 'ordenes';
    public $timestamps = false;

    protected $fillable = [
        'users_id', 
        'servicios_id',
        'nota_orden',
        'precio_envio',
        'precio_total',
        'fecha_orden',
        'cambio',
        'estado_2',
        'fecha_2',
        'hora_2',
        'estado_3',
        'fecha_3',
        'estado_4',
        'fecha_4',
        'estado_5',
        'fecha_5',
        'estado_6',
        'fecha_6',
        'estado_7',
        'fecha_7',
        'estado_8',
        'fecha_8',
        'mensaje_8',
        'visible_p',
        'visible_p2',
        'tardio',
        'cancelado_cliente',
        'cancelado_propietario',
       
        'visible_p3'
    ];

}
