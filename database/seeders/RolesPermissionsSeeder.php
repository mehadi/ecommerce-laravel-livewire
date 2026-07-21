<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Product permissions
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Category permissions
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Order permissions
            'view orders',
            'edit orders',
            'delete orders',
            'process orders',

            // Coupon permissions
            'view coupons',
            'create coupons',
            'edit coupons',
            'delete coupons',

            // Landing page sections permissions
            'view sections',
            'create sections',
            'edit sections',
            'delete sections',

            // Testimonial permissions
            'view testimonials',
            'create testimonials',
            'edit testimonials',
            'delete testimonials',

            // Settings permissions
            'view settings',
            'edit settings',

            // Inventory permissions
            'view inventory',
            'adjust stock',
            'manage inventory settings',

            // Warehouse permissions
            'view warehouses',
            'create warehouses',
            'edit warehouses',
            'delete warehouses',

            // Stock transfer permissions
            'view transfers',
            'create transfers',
            'receive transfers',
            'cancel transfers',

            // Supplier permissions
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',

            // Purchase order permissions
            'view purchase orders',
            'create purchase orders',
            'receive purchase orders',
            'cancel purchase orders',

            // Cycle count permissions
            'view cycle counts',
            'create cycle counts',
            'complete cycle counts',

            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role & Permission permissions
            'manage roles',
            'manage permissions',

            // POS permissions
            'access pos',
            'process pos sales',
            'apply pos discounts',
            'hold pos sales',
            'void pos sale line',
            'void pos sale',
            'process pos refunds',
            'open pos shift',
            'close pos shift',
            'force close pos shift',
            'manage cash drawer',
            'view pos reports',
            'manage pos registers',
            'manage pos settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create Roles - Super Admin first (has all permissions and cannot be modified)
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super admin'],
            ['guard_name' => 'web']
        );

        // Create Roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['guard_name' => 'web']
        );

        $editorRole = Role::firstOrCreate(
            ['name' => 'editor'],
            ['guard_name' => 'web']
        );

        // Cashier: POS terminal operation only — no backoffice access, no
        // refunds, no register/shift/report management (see the permission
        // matrix in docs/pos-module/SPEC.md §4.2).
        $cashierRole = Role::firstOrCreate(
            ['name' => 'cashier'],
            ['guard_name' => 'web']
        );

        // Assign all permissions to super admin (always has everything)
        $superAdminRole->syncPermissions(Permission::all());

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign manager permissions (everything except roles/permissions
        // management and the tenant-wide POS settings screen, which is
        // reserved for admin/super admin).
        $managerPermissions = Permission::whereNotIn('name', ['manage roles', 'manage permissions', 'manage pos settings'])->get();
        $managerRole->syncPermissions($managerPermissions);

        // Assign editor permissions (view and edit, but no delete or create)
        $editorPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', 'view %')
                ->orWhere('name', 'like', 'edit %')
                ->orWhere('name', '=', 'process orders');
        })->get();
        $editorRole->syncPermissions($editorPermissions);

        $cashierPermissions = Permission::whereIn('name', [
            'access pos',
            'process pos sales',
            'apply pos discounts',
            'hold pos sales',
            'void pos sale line',
            'open pos shift',
            'close pos shift',
            'manage cash drawer',
        ])->get();
        $cashierRole->syncPermissions($cashierPermissions);
    }
}
