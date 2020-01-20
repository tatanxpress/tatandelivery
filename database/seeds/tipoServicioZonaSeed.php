<?php

use App\TipoServiciosZona;
use Illuminate\Database\Seeder;

class tipoServicioZonaSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoServiciosZona::insert([
            [ 'tipo_servicios_id' => '1', // restaurante
              'zonas_id' => '1', // metapan
              'activo' => '1',
            ]        
        ]); 

        TipoServiciosZona::insert([
            [ 'tipo_servicios_id' => '2', //tienda
              'zonas_id' => '1', // metapan
              'activo' => '1',
            ]        
        ]); 

        TipoServiciosZona::insert([
            [ 'tipo_servicios_id' => '1', // comida solamente
              'zonas_id' => '2', // para zona unicaes
              'activo' => '1', 
            ]
        ]); 
              
    
    }
}
