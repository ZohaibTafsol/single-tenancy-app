<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        /**
         * 1. Define Permissions (adjust as per your system)
         */
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'permissions.view',
            'permissions.assign',

            'invoices.view',
            'invoices.create',
            'invoices.approve',
            'invoices.delete',

            'payers.view',
            'payers.create',
            'payers.edit',
            'payers.delete',

            'receipients.view',
            'receipients.create',
            'receipients.edit',
            'receipients.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * 2. Define Roles
         */
        $roles = [
            'admin' => Permission::all(),
            'manager' => [
                'users.view',
                'users.create',
                'users.edit',
                'invoices.view',
                'invoices.create',
            ],
            'accountant' => [
                'invoices.view',
                'invoices.create',
                'invoices.approve',
            ],

            // Custom role you requested
            'tax1099' => [
                'invoices.view',
                'invoices.create',
            ],
        ];

        /**
         * 3. Create roles and sync permissions
         */
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($rolePermissions instanceof \Illuminate\Support\Collection) {
                $role->syncPermissions($rolePermissions);
            } else {
                $role->syncPermissions($rolePermissions);
            }
        }
    }
}
