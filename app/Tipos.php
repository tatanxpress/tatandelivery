<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipos extends Model
{
    protected $table = 'tipos';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'descripcion'
    ];
}
