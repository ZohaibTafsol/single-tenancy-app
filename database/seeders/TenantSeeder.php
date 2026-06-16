<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant_one = \App\Models\Tenant::create([
            'id' => 'tenant-one',
            'uuid' => Str::uuid(),
            'name' => 'Tenant One',
        ]);
        $tenant_one->domains()->create([
            'domain' => 'tenant-one.localhost',
        ]);

        $tenant_two = \App\Models\Tenant::create([
            'id' => 'tenant-two',
            'name' => 'Tenant Two',
        ]);
        $tenant_two->domains()->create([
            'domain' => 'tenant-two.localhost',
        ]);

        $tenant_three = \App\Models\Tenant::create([
            'id' => 'tenant-three',
            'name' => 'Tenant Three',
        ]);
        $tenant_three->domains()->create([
            'domain' => 'tenant-three.localhost',
        ]);
    }
}
