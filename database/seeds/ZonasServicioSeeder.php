<?php

use App\ZonasServicios;
use Illuminate\Database\Seeder;

class ZonasServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ZonasServicios::insert([
            [ 'zonas_id' => '1',
              'servicios_id' => '1',
              'precio_envio' => '1.00', 
              'activo' => '1', 
            ]        
        ]); 

        ZonasServicios::insert([
            [ 'zonas_id' => '1',
              'servicios_id' => '2',
              'precio_envio' => '1.25', 
              'activo' => '1', 
            ]        
        ]); 

        ZonasServicios::insert([
            [ 'zonas_id' => '2',
              'servicios_id' => '1',
              'precio_envio' => '1.50', 
              'activo' => '1',
            ]        
        ]); 
    }
}
