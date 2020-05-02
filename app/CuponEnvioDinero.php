<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuponEnvioDinero extends Model
{
    // cupon para envio gratis, minimo de compra para poder aplicarse
    protected $table = 'c_envio_dinero';
    public $timestamps = false;
}
