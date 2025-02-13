<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'usuarios',]);
        Permission::create(['name' => 'roles']);
        Permission::create(['name' => 'articulos']);
        Permission::create(['name' => 'categorias']);
        Permission::create(['name' => 'servicios']);
        Permission::create(['name' => 'ventas']);
        Permission::create(['name' => 'compras']);
        Permission::create(['name' => 'clientes']);
        Permission::create(['name' => 'proveedores']);
        Permission::create(['name' => 'impuestos']);
        Permission::create(['name' => 'configuracion']);

        $role = Role::create(['name' => 'Administrator']);
        
        $role->givePermissionTo('usuarios');
        $role->givePermissionTo('roles');
        $role->givePermissionTo('articulos');
        $role->givePermissionTo('categorias');
        $role->givePermissionTo('servicios');
        $role->givePermissionTo('ventas');
        $role->givePermissionTo('compras');
        $role->givePermissionTo('clientes');
        $role->givePermissionTo('proveedores');
        $role->givePermissionTo('impuestos');
        $role->givePermissionTo('configuracion');
        
    }
}
