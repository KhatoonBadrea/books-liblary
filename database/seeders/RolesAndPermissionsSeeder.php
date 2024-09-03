<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'store user']);
        Permission::create(['name' => 'index user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'show user']);

        // إنشاء الأدوار وتخصيص الأذونات
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo('store user');
        $adminRole->givePermissionTo('index user');
        $adminRole->givePermissionTo(['update user', 'delete user', 'show user']);

        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo(['update user', 'delete user', 'show user']);
    }
}
