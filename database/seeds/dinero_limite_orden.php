<?php

use Illuminate\Database\Seeder;
use App\DineroOrden;
class dinero_limite_orden extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = DineroOrden::create([ 
            'limite' => '125.00',
            'ver_cupones' => '1'
            ]); 
    }
}
