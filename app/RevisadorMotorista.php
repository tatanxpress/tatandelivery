<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevisadorMotorista extends Model
{
    protected $table = 'revisador_motoristas';
    public $timestamps = false;

    protected $fillable = [
        'revisador_id', 'motoristas_id'
    ];
}
