<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MotoristaPago extends Model
{
    protected $table = 'motorista_pago';
    public $timestamps = false;

    protected $fillable = ['motorista_id', 'pago', 'fecha1', 'fecha2', 'fecha'];
}
