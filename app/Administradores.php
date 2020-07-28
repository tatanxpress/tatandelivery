<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Administradores extends Model
{
    protected $table = 'administradores';    
    public $timestamps = false;
    protected $fillable = [
        'nombre', 'telefono', 'password', 'device_id', 'activo', 'disponible'
    ];    

}
