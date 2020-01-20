<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenesPendiente extends Model
{
    // cuando hay una orden que llego al estado 5 y aun no tiene motorista asignado
    protected $table = 'ordenes_pendiente';
    public $timestamps = false;

    protected $fillable = ['ordenes_id', 'fecha', 'activo', 'tipo'];
     
} 
 