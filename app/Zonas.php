<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zonas extends Model
{
    protected $table = 'zonas';
    public $timestamps = false;
    protected $fillable = [
        'nombre', 'latitud', 'longitud', 'hora_abierto_delivery', 'hora_cerrado_delivery', 'fecha', 'activo', 'identificador'
    ];
}
