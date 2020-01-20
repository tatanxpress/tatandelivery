<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiempoAproximado extends Model
{ 
    protected $table = 'tiempo_aprox';
    public $timestamps = false;

    protected $fillable = [
        'servicios_id', 'dia', 'tiempo'
    ];
}
