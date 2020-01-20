<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoServiciosZona extends Model
{
    protected $table = 'tipo_servicios_zonas';
    public $timestamps = false;

    protected $fillable = [
        'tipo_servicios_id', 'zonas_id', 'activo'
    ];
}
