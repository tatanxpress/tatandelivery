<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revisador extends Model
{
    protected $table = 'revisador';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'direccion', 'telefono', 'latitud', 'longitud', 'password', 'disponible', 'activo', 'codigo'
    ];
}
