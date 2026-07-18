<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformAdminSeeder extends Seeder
{
    /**
     * Platform staff are identified purely by tenant_id === null (see
     * Gate::define('access platform') in AppServiceProvider) — no role needed.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'platformadmin@example.com'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('password'),
                'tenant_id' => null,
                'can_impersonate_tenants' => true,
            ]
        );
    }
}
