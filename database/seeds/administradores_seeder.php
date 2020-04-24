<?php

use App\Admin;
use Illuminate\Database\Seeder;
class administradores_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $user = Admin::create([ 
            'nombre' => 'Jonathan Moran', 
            'email' => 'tatanadmin@gmail.com',
            'password' => bcrypt('12345678')]); 
            
        $user->assignRole('super-admin');

        
        $user2 = Admin::create([ 
            'nombre' => 'Eduardo', 
            'email' => 'eduardo@gmail.com',
            'password' => bcrypt('12345678')]); 
            
        $user2->assignRole('editor'); 
       
    }
}