<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevisadorBitacora extends Model
{
    protected $table = 'bitacora_revisador';
    public $timestamps = false;

    protected $fillable = [
        'revisador_id', 'fecha1', 'fecha2', 'total', 'confirmadas'
    ];
}
