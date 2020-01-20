<?php

use App\Servicios;
use Illuminate\Database\Seeder;

class serviciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Servicios::insert([
            [ 'nombre' => 'Taqueria Metapaneca',
              'descripcion' => 'Tacos - Burritos - Enchiladas',
              'logo' => 'p2.jpg',
              'imagen' => 'salsa2.jpg',
              'cerrado_emergencia' => '0',
              'fecha' => '2019-10-11',
              'activo' => '1',
              'tipo_servicios_id' => '1',
              'envio_gratis' => 0,
              'telefono' => '24021545',
              'latitud' => '',
              'longitud' => '',
              'direccion' => 'las americas 1',
              ]
        ]); 

        Servicios::insert([
            [ 'nombre' => 'Tienda Xpress',
              'descripcion' => 'Canasta basica',
              'logo' => 'tienda.jpg',
              'imagen' => 'tienda.jpg',
              'cerrado_emergencia' => '0',
              'fecha' => '2019-10-11',
              'activo' => '1',
              'tipo_servicios_id' => '2',
              'envio_gratis' => 0,
              'telefono' => '24021545',
              'latitud' => '',
              'longitud' => '',
              'direccion' => 'calle las parejas 1',
              ]
        ]); 
    }
}
