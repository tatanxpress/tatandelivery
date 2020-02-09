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
        /*$user = Admin::insert([
              'nombre' => 'Editor', 
              'email' => 'editor2@gmail.com',
              'password' => bcrypt('12345678'),             
              'activo' => 1         
        ]);

        $user->assignRole('read-roles');*/

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
 
       /* $moderador = Admin::insert([
            [ 'nombre' => 'Moderador', 
              'email' => 'moderador@gmail.com',
              'password' => bcrypt('12345678'),
              'avatar' => 'tlogo.jpg',
              'activo' => 1]           
        ]);

        $moderador->assignRole('moderador');

        $admin = Admin::insert([
            [ 'nombre' => 'Administrador', 
              'email' => 'admin@gmail.com',
              'password' => bcrypt('12345678'),
              'avatar' => 'tlogo.jpg',
              'activo' => 1]           
        ]);

        $admin->assignRole('super-admin');*/
    }
}