<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagoPropietario extends Model
{
    protected $table = 'pago_propietario';
    public $timestamps = false;

    protected $fillable = ['fecha', 'fecha_pago', 'completadas', 'cancelada_propietario',
                        'cancelada_cliente', 'cancelada_tardio', 'total_generado',
                    'descuento', 'total', 'nota', 'servicios_id'];
    
}
