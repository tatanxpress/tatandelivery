<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    protected $guard = 'admin';

    protected $table = 'admins';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'email', 'password'
    ];

    protected $hidden   = ['password']; 
}
