<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
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
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
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
            ['name' => 'accountant', 'guard_name' => 'web']
        );
        $supervisor->assignRole($supervisorRole);
    }
}
