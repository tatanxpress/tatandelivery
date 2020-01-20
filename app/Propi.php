<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Propi extends Authenticatable
{
    use Notifiable;

    protected $guard = 'propi';

    protected $table = 'propietarios';
    public $timestamps = false;

    protected $fillable = ['nombre', 'telefono', 'password', 'direccion', 'correo',
    'fecha', 'dui', 'disponibilidad', 'device_id', 'servicios_id', 'tipo_propietario', 'codigo_correo'];

    protected $hidden   = ['password']; 
}
