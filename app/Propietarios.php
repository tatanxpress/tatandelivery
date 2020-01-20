<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Propietarios extends Model
{
    protected $table = 'propietarios';
    public $timestamps = false;

    protected $fillable = ['nombre', 'telefono', 'password', 'correo', 'fecha', 'dui', 'disponibilidad', 'device_id', 'servicios_id', 'codigo_correo', 'activo'];

}
