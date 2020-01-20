<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZonaPublicidad extends Model
{
    protected $table = 'zonas_publicidad';
    public $timestamps = false;
    protected $fillable = [
        'zonas_id', 'publicidad_id', 'posicion', 'fecha'
    ];
}
