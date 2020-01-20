<?php

use App\TiempoAproximado;
use Illuminate\Database\Seeder;

class TiempoAproxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TiempoAproximado::insert([
            [ 'servicios_id' => '1',
              'dia' => '2',
              'tiempo' => '45 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '1',
              'dia' => '1',
              'tiempo' => '15 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '1',
              'dia' => '3',
              'tiempo' => '10 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '1',
              'dia' => '4',
              'tiempo' => '5 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '1',
              'dia' => '5',
              'tiempo' => '18 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '1',
              'dia' => '6',
              'tiempo' => '23 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '1',
              'dia' => '7',
              'tiempo' => '1 hora',
              ]
        ]); 



        TiempoAproximado::insert([
            [ 'servicios_id' => '2',
              'dia' => '1',
              'tiempo' => '35 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '2',
              'dia' => '2',
              'tiempo' => '17 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '2',
              'dia' => '3',
              'tiempo' => '23 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '2',
              'dia' => '4',
              'tiempo' => '24 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '2',
              'dia' => '5',
              'tiempo' => '29 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '2',
              'dia' => '6',
              'tiempo' => '33 min',
              ]
        ]); 

        TiempoAproximado::insert([
            [ 'servicios_id' => '2',
              'dia' => '7',
              'tiempo' => '47 min',
              ]
        ]); 
    }
}
