<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MotoristaOrdenes extends Model
{
    protected $table = 'motorista_ordenes';
    public $timestamps = false;

    protected $fillable = ['ordenes_id', 'motoristas_id'];
    
}
