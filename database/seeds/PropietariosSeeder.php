<?php

use App\Propietarios;
use Illuminate\Database\Seeder;

class PropietariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Propietarios::insert([
            [ 'nombre' => '14.303109',
              'telefono' => '-89.423752',
              'password' => '$2y$10$uLltn9ue7.9oHtqkA/aUcOpVftwPmwlg/xi1mnyenyoA/t21r0hYe',
              'direccion' => 'carretera internacional, frente al super selectos',
              'correo' => 'guada@gmail.com',
              'fecha' => '2019-11-05',
              'disponibilidad' => '1',
              'activo' => '1',
              'dui' => '36523584-0',
            ]        
        ]); 
    }
}
