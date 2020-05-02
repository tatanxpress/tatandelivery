<?php

use Illuminate\Database\Seeder;
use App\TipoCupon;

class Cupones_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipo1 = TipoCupon::create([ 
            'nombre' => 'Envío Gratis',
            'descripcion' => 'Aplicara para cualquier zona y cualquier servicio'
            ]); 

        $tipo2 = TipoCupon::create([ 
            'nombre' => 'Descuento $',
            'descripcion' => 'Aplicara para cualquier servicio '
            ]); 

        $tipo3 = TipoCupon::create([ 
            'nombre' => 'Descuento %',
            'descripcion' => 'Aplicara para cualquier servicio, necesita minimo de compra'
            ]); 

        $tipo4 = TipoCupon::create([ 
            'nombre' => 'Producto Gratis',
            'descripcion' => 'Aplicara para cualquier servicio, necesita minimo de compra'
            ]); 
    }
}
