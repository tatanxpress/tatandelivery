<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenesUrgentes extends Model
{
    protected $table = 'ordenes_urgentes';
    public $timestamps = false;

    protected $fillable = ['ordenes_id', 'fecha', 'activo', 'tipo'];
}
 