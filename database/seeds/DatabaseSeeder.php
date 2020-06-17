<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      /*$this->call(RolesAndPermissions::class);
      $this->call(administradores_seeder::class);
      $this->call(dinero_limite_orden::class);
      $this->call(Cupones_Seeder::class);*/
      $this->call(VersionApp::class);
    }
} 
