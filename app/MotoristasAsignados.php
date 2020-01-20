<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MotoristasAsignados extends Model
{
    protected $table = 'motoristas_asignados';
    public $timestamps = false;

    protected $fillable = ['servicios_id', 'motoristas_id'];

}
