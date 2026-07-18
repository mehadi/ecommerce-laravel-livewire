<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Roles are created by RolesPermissionsSeeder
        // Just ensure roles exist (should already exist)
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super admin'],
            ['guard_name' => 'web']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );

        // Create super admin user (store staff for the current tenant — User isn't
        // scoped by BelongsToTenant, so tenant_id is set explicitly here)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'tenant_id' => Tenancy::id(),
            ]
        );

        if (! $superAdmin->hasRole('super admin')) {
            $superAdmin->assignRole('super admin');
        }

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'tenant_id' => Tenancy::id(),
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
