<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenesUrgentesCuatro extends Model
{
    // paso la mitad de tiempo que el propietario dijo que entregarian la orden
    // ningun motorista agarro la orden

    protected $table = 'ordenes_urgentes_cuatro';
    public $timestamps = false;
}
