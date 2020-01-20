<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistroPromo extends Model
{
    protected $table = 'registro_promo';
    public $timestamps = false;

    protected $fillable = [
        'servicios_id', 'fecha1', 'fecha2', 'fecha', 'tipo', 'pago', 'descripcion'
    ];
}
