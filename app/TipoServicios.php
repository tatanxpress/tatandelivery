<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoServicios extends Model
{
    protected $table = 'tipo_servicios';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'descripcion', 'imagen'
    ];
}
