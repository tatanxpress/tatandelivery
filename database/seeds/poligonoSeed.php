<?php

use App\Poligono;
use Illuminate\Database\Seeder;

class poligonoSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Poligono::insert([
            [ 'latitud' => '14.334585',
              'longitud' => '-89.443032',
              'zonas_id' => '1']        
        ]); 

        Poligono::insert([
            [ 'latitud' => '14.331277',
              'longitud' => '-89.446870',
              'zonas_id' => '1']        
        ]); 

        Poligono::insert([
            [ 'latitud' => '14.327377',
              'longitud' => '-89.439174',
              'zonas_id' => '1']        
        ]); 

        Poligono::insert([
            [ 'latitud' => '14.332090',
              'longitud' => '-89.436390',
              'zonas_id' => '1']        
        ]); 

        // unicaes

        Poligono::insert([
            [ 'latitud' => '14.304244',
              'longitud' => '-89.425052',
              'zonas_id' => '2']        
        ]); 

        Poligono::insert([
            [ 'latitud' => '14.303072',
              'longitud' => '-89.425903',
              'zonas_id' => '2']        
        ]); 

        Poligono::insert([
            [ 'latitud' => '14.302152',
              'longitud' => '-89.424619',
              'zonas_id' => '2']        
        ]); 

        Poligono::insert([
            [ 'latitud' => '14.303109',
              'longitud' => '-89.423752',
              'zonas_id' => '2']        
        ]); 
    }
}
