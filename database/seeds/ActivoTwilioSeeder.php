<?php

use Illuminate\Database\Seeder;
use App\ActivoSms;

class ActivoTwilioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ActivoSms::insert([
            [ 'activo' => 1,
            ]        
        ]); 
    }
}
