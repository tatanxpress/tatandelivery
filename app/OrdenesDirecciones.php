<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenesDirecciones extends Model
{
    protected $table = 'ordenes_direcciones';
    public $timestamps = false;

    protected $fillable = ['users_id', 
    'ordenes_id',
     'zonas_id', 
     'nombre', 
     'direccion',
     'numero_casa', 
     'punto_referencia', 
     'telefono', 
     'latitud', 
     'longitud'];
    
}
