<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivoSms extends Model
{
    protected $table = 'activo_sms';
    public $timestamps = false;
    protected $fillable = ['activo'];

    
}
