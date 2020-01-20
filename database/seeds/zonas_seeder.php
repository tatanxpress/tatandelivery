<?php

use App\Zonas;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class zonas_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateString();
        Zonas::insert([
            ['nombre' => 'Metapan',
              'latitud' => '14.332202',
              'longitud' => '-89.448206',
              'hora_abierto_delivery' => '08:00:00',
              'hora_cerrado_delivery' => '21:00:00',
              'saturacion' => '0',
              'fecha' => $now]        
        ]);

        Zonas::insert([
            ['nombre' => 'Unicaes',
              'latitud' => '14.302939',
              'longitud' => '-89.424646',
              'hora_abierto_delivery' => '09:00:00',
              'hora_cerrado_delivery' => '15:00:00',
              'saturacion' => '0',
              'fecha' => $now]        
        ]); 

    }
}
