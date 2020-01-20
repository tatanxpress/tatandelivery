<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direccion_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'direccion', 'numero_casa', 'punto_referencia', 'telefono', 'seleccionado', 'latitud', 'longitud', 'zonas_id', 'user_id'
    ];
}
