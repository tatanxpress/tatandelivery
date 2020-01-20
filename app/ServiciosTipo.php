<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiciosTipo extends Model
{
    protected $table = 'servicios_tipo';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'servicios_1_id', 'posicion', 'activo', 'fecha'
    ];
}
