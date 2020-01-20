<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZonasServicios extends Model
{
    protected $table = 'zonas_servicios';
    public $timestamps = false;
    protected $fillable = [
        'zonas_id', 'servicios_id', 'precio_envio', 'activo', 'posicion'
    ];
}
