<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{
    protected $table = 'servicios';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 
        'identificador',
        'descripcion', 
        'logo', 
        'imagen',
        'cerrado_emergencia', 
        'fecha', 
        'activo', 
        'tipo_servicios_id', 
        'envio_gratis', 
        'telefono', 
        'latitud', 
        'longitud', 
        'direccion',
        'tipo_vista', 
        'minimo', 
        'utiliza_minimo', 
        'orden_automatica', 
        'tiempo',
        'tiempo_orden_max',
        'producto_visible',
        'comision',
        'prestar_motorista'
    ];
}
 