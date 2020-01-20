<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicioPago extends Model
{
    protected $table = 'servicio_pago';
    public $timestamps = false;

    protected $fillable = ['servicios_id', 'pago', 'fecha1', 'fecha2', 'fecha'];
}
