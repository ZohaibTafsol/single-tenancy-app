<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Admin',
            'email' => 'admin@tax.com',
            'password' => bcrypt('123123123'),
        ]);
        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);


        $permissions = Permission::pluck('id')->toArray();

        // Sync all permissions to the role
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);

        /** -----------------------
         *  SUPERVISOR ROLE & USER
         *  ----------------------*/
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@tax.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Supervisor',
                'password' => bcrypt('123123123'),
            ]
        );

        $supervisorRole = Role::firstOrCreate(
            ['name' => 'Supervisor', 'guard_name' => 'web']
        );

        // only user-related permissions
        $userPermissions = Permission::whereIn('name', [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
        ])->pluck('name')->toArray();

        $supervisorRole->syncPermissions($userPermissions);
        $supervisor->assignRole($supervisorRole);
    }
}
