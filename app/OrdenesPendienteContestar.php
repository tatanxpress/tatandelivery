<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenesPendienteContestar extends Model
{
    protected $table = 'orden_pendiente_contestar';
    public $timestamps = false;

    protected $fillable = ['ordenes_id', 'fecha', 'activo', 'tipo'];
}
