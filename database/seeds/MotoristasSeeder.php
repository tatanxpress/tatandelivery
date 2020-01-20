<?php

use App\Motoristas;
use Illuminate\Database\Seeder;

class MotoristasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Motoristas::insert([
            [ 'nombre' => 'Juan Miguel',
              'telefono' => '7575',
              'correo' => 'juan@gmail.com',
              'password' => '$2y$10$uLltn9ue7.9oHtqkA/aUcOpVftwPmwlg/xi1mnyenyoA/t21r0hYe',
              'tipo_vehiculo' => 'Motocicleta',
              'numero_vehiculo' => 'P 56524-41',
              'activo' => '1',
              'disponible' => '1',
              'fecha' => '2019-11-05',
              'dui' => '236521465-0',
              'imagen' => 'moto1.jpg'
            ]        
        ]); 
        
    }
}
