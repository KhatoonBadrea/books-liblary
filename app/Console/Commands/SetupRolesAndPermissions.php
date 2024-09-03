<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupRolesAndPermissions extends Command
{
    protected $signature = 'setup:roles';

    protected $description = 'Setup initial roles and permissions';

    public function handle()
    {
        Permission::create(['name' => 'edit articles']);


        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo('edit articles');

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
