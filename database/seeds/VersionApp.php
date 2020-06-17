<?php

use Illuminate\Database\Seeder;
use App\VersionesApp;

class VersionApp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datos = VersionesApp::create([ 
            'android' => '1.27',
            'iphone' => '1.50',
            'activo' => '0',
            'activo_iphone' => '0'
            ]); 
    }
}
