<?php

use App\ServiciosTipo;
use Illuminate\Database\Seeder;

class ServiciosTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ServiciosTipo::insert([
            [ 'nombre' => 'Seccion carnes',
              'servicios_1_id' => '1',
              'posicion' => '3',
              'activo' => 1
              
              ]
        ]); 

        ServiciosTipo::insert([
            [ 'nombre' => 'Seccion Bebidas',
              'servicios_1_id' => '1',
              'posicion' => '2',
              'activo' => 1,
             
              ]
        ]); 

        ServiciosTipo::insert([
            [ 'nombre' => 'Seccion cereal',
              'servicios_1_id' => '2',
              'posicion' => '1',
              'activo' => 1,
                           
              ]
        ]); 

        ServiciosTipo::insert([
            [ 'nombre' => 'Seccion bebidas',
              'servicios_1_id' => '2',
              'posicion' => '2',
              'activo' => 1,
              
              ]
        ]); 

    }
}
