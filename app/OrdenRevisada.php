<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenRevisada extends Model
{
    protected $table = 'ordenes_revisadas';
    public $timestamps = false;

    protected $fillable = ['ordenes_id', 'fecha', 'revisador_id'];
}
