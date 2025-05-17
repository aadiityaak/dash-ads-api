<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //buat permission jika belum ada
        $permissions = [
            'page-dashboard',
            'page-users',
            'view-other-user',
            'edit-other-user',
            'create-other-user',
            'edit-user',
            'delete-user',
            'edit-settings',
            'create-post',
            'edit-post',
            'delete-post',
            'edit-term'
        ];

        foreach ($permissions as $permission) {
            //check permission
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
                $this->command->info('Permission created: ' . $permission);
            }
        }

        //buat default role
        $roles = [
            'admin',
            'owner',
            'user',
        ];

        foreach ($roles as $role) {
            //check role
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
                $this->command->info('Role created: ' . $role);
            }

            if ($role == 'admin') {
                $role_admin = Role::where('name', $role)->first();
                $role_admin->givePermissionTo(Permission::all());
            } elseif ($role == 'owner') {
                $role_owner = Role::where('name', $role)->first();
                $role_owner->givePermissionTo([
                    'page-dashboard',
                    'edit-settings',
                    'create-post',
                    'edit-post',
                    'delete-post',
                    'edit-user',
                    'delete-user',
                ]);
            } else {
                $role_user = Role::where('name', $role)->first();
                $role_user->givePermissionTo([
                    'page-dashboard',
                    'edit-user',
                    'delete-user',
                ]);
            }
        }

        // Role::create(['name' => 'manager_project']);
        // Role::create(['name' => 'manager_advertising']);
        // Role::create(['name' => 'finance']);
        // Role::create(['name' => 'support']);
        // Role::create(['name' => 'revisi']);
        // Role::create(['name' => 'advertising']);
        // Role::create(['name' => 'webdev']);
        // Role::create(['name' => 'advertising_internal']);
        // Role::create(['name' => 'budi']);

    }
}
