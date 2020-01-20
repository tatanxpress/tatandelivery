<?php

use App\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Producto::insert([
            [ 'servicios_tipo_id' => '1',
              'nombre' => 'Tortas mixta',
              'imagen' => '',
              'descripcion' => 'Ricas tortas mixtas, lleva carne, arroz, frijol',
              'precio' => '3.50',
              'unidades' => '4',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '2',
              'fecha' => '2019-10-11',
              'utiliza_cantidad' => '1']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '1',
              'nombre' => 'Tacos de res',
              'imagen' => 'taco.jpg',
              'descripcion' => 'ricos tacos de orden de 4, salsa extra necesita para darle un toque de sabor mas rico a tu producto de pruyeba, compralo',
              'precio' => '3.50',
              'unidades' => '2',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-11',
              'utiliza_cantidad' => '1']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '2',
              'nombre' => 'coca cola lata',
              'imagen' => 'coca.jpg',
              'descripcion' => 'coca lata 150 ml',
              'precio' => '0.50',
              'unidades' => '10',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '3',
              'nombre' => 'mirinda lata',
              'imagen' => 'coca.jpg',
              'descripcion' => 'lata 500ml',
              'precio' => '0.50',
              'unidades' => '1',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '3',
              'nombre' => 'Salutai naranja',
              'imagen' => 'coca.jpg',
              'descripcion' => 'lata 500ml',
              'precio' => '0.50',
              'unidades' => '1',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '2',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '3',
              'nombre' => 'Pepsi lata',
              'imagen' => 'coca.jpg',
              'descripcion' => 'lata 500ml',
              'precio' => '0.75',
              'unidades' => '1',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '3',
              'nombre' => 'salutari simple',
              'imagen' => 'coca.jpg',
              'descripcion' => 'lata 500ml',
              'precio' => '0.45',
              'unidades' => '10',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '3',
              'nombre' => 'coca cola zero',
              'imagen' => 'coca.jpg',
              'descripcion' => 'lata 500ml',
              'precio' => '1.25',
              'unidades' => '1',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '3',
              'nombre' => 'salutary limon',
              'imagen' => 'coca.jpg',
              'descripcion' => 'lata 500ml',
              'precio' => '1.00',
              'unidades' => '1',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '3',
              'nombre' => 'coca cola vidrio',
              'imagen' => 'coca.jpg',
              'descripcion' => 'lata 500ml',
              'precio' => '1.00',
              'unidades' => '1',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '4',
              'nombre' => 'kellog grande',
              'imagen' => 'k1.jpg',
              'descripcion' => 'caja mediana 30 g',
              'precio' => '3.25',
              'unidades' => '11',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']        
        ]); 

        Producto::insert([
            [ 'servicios_tipo_id' => '4',
              'nombre' => 'kellog azucarados',
              'imagen' => 'k3.jpg',
              'descripcion' => 'caja de kellog 250 g',
              'precio' => '5.34',
              'unidades' => '3',
              'disponibilidad' => '1',
              'activo' => '1',
              'posicion' => '1',
              'fecha' => '2019-10-22',
              'utiliza_cantidad' => '0']
        ]); 

    }
}
