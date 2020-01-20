<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoTemporal extends Model
{
    protected $table = 'codigo_temporal';
    public $timestamps = false;

    protected $fillable = [
        'telefono, codigo', 'contador'
    ];
}
