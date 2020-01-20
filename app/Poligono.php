<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poligono extends Model
{
    protected $table = 'poligono_array';
    public $timestamps = false;

    protected $fillable = [
        'latitud', 'longitud', 'zonas_id'
    ];
}
