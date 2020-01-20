<?php

use App\TipoServicios;
use Illuminate\Database\Seeder;

class tipo_servicio_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoServicios::insert([
              'nombre' => 'Restaurante',
              'imagen' => 'comida.jpg',
        ]);

        TipoServicios::insert([
              'nombre' => 'Tienda',
              'imagen' => 'tiendalogo.jpg',
        ]);
    }
}
