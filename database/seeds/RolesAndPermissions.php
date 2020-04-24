<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Reset cached roles and permissions
       app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
      
       // create permissions
       Permission::create(['guard_name' => 'admin', 'name' => 'completo']);
       Permission::create(['guard_name' => 'admin', 'name' => 'limitado']);
    
        // this can be done as separate statements
        $role = Role::create(['name' => 'editor']);
        $role->givePermissionTo('limitado');
       
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo('completo');
    }
}
