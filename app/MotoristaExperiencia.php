<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MotoristaExperiencia extends Model
{
    protected $table = 'motorista_experiencia';
    public $timestamps = false;

    protected $fillable = ['ordenes_id', 'motoristas_id', 'experiencia', 'mensaje', 'fecha'];


}
