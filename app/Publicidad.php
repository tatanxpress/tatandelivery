<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publicidad extends Model
{
    protected $table = 'publicidad';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'logo', 'imagen',
    'tipo_publicidad', 'url_facebook', 'utiliza_facebook', 'url_youtube',
    'utiliza_youtube', 'url_instagram', 'utiliza_instagram', 'titulo',
    'utiliza_titulo', 'titulo_descripcion', 'utiliza_descripcion','telefono',
    'utiliza_telefono', 'posicion', 'activo', 'utiliza_visitanos',
    'visitanos', 'fecha_inicio', 'fecha_fin'];
}
 