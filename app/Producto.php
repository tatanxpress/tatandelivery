<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    public $timestamps = false;

    protected $fillable = [ 'servicios_tipo_id', 
    'nombre', 
    'imagen', 
    'descripcion',
    'precio', 
    'unidades', 
    'disponibilidad', 
    'activo',  
    'posicion', 
    'utiliza_cantidad', 
    'fecha',
    'es_promocion',
    'limite_orden',
    'cantidad_por_orden',
    'utiliza_cantidad',
    'nota'
    ];
}
