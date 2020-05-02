<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoCupon extends Model
{
    protected $table = 'tipo_cupon';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];
   
}
