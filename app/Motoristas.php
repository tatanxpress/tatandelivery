<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Motoristas extends Model
{
    protected $table = 'motoristas';
    public $timestamps = false;

    protected $fillable = ['nombre', 'telefono', 'correo', 'password', 'tipo_vehiculo', 'numero_vehiculo',
        'activo', 'disponible', 'fecha', 'dui', 'device_id', 'imagen', 'licensia', 'codigo_correo', 'zona_pago', 'limite_ordenes'];
}
