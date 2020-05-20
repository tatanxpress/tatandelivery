<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenesUrgentesTres extends Model
{

    // pasaron 5+ de hora entrega al cliente (hora_2 + zona + 5+) y no se ha entregado su orden
    // tabla ordenes_urgentes_tres
    protected $table = 'ordenes_urgentes_tres';
    public $timestamps = false;
}
