<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HorarioServicio extends Model
{
    protected $table = 'horario_servicio';    
    public $timestamps = false;
    protected $fillable = [
        'servicios_id', 'hora1', 'hora2', 'hora3', 'hora4', 'dia', 'segunda_hora', 'cerrado'
    ];
}
