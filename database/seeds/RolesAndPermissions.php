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
       // app()['cache']->forget('spatie.permission.cache');
 
       //$role = Role::create(['name' => 'writer']);
        //$permission = Permission::create(['name' => 'edit articles']);

       // create permissions
       Permission::create(['guard_name' => 'admin', 'name' => 'completo']);
       Permission::create(['guard_name' => 'admin', 'name' => 'limitado']);
      /* Permission::create(['guard_name' => 'admin', 'name' => 'update user']);
       Permission::create(['guard_name' => 'admin', 'name' => 'delete user']);

       Permission::create(['guard_name' => 'admin', 'name' => 'create roles']);
       Permission::create(['guard_name' => 'admin', 'name' => 'read roles']);
       Permission::create(['guard_name' => 'admin', 'name' => 'update role']);
       Permission::create(['guard_name' => 'admin', 'name' => 'delete role']);

       Permission::create(['guard_name' => 'admin', 'name' => 'create permission']);
       Permission::create(['guard_name' => 'admin', 'name' => 'read permissions']);
       Permission::create(['guard_name' => 'admin', 'name' => 'update permission']);
       Permission::create(['guard_name' => 'admin', 'name' => 'delete permission']);*/

     
        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'editor']);
        $role->givePermissionTo('limitado');

        /*$role = Role::create(['name' => 'moderador']);
        $role->givePermissionTo('create user');
        $role->givePermissionTo('read users');
        $role->givePermissionTo('update user');
        $role->givePermissionTo('delete user');*/

        // or may be done by chaining
        //$role = Role::create(['name' => 'moderator'])
       //     ->givePermissionTo(['publish articles', 'unpublish articles']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo('completo');
    }
}
