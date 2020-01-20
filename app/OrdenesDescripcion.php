<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenesDescripcion extends Model
{
    protected $table = 'ordenes_descripcion';
    public $timestamps = false;

    protected $fillable = ['ordenes_id', 'producto_id', 'cantidad', 'nota', 'precio'];
    
}
 