<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'web';

        // 1. Sync all permissions from config
        foreach (config('permissions', []) as $group => $permissions) {
            foreach (array_keys($permissions) as $name) {
                Permission::findOrCreate($name, guardName: $guard);
            }
        }

        // WithoutModelEvents disables cache refresh on create; manually clear so syncPermissions sees new permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $allPermissions = collect(config('permissions', []))->flatMap(fn ($p) => array_keys($p))->unique()->values()->all();

        // 2. Admin role - all permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $admin->syncPermissions($allPermissions);

        // 3. Staff role - limited permissions
        $staffPermissions = [
            'products.view', 'products.create', 'products.edit',
            'users.view',
            'pages.view', 'pages.edit',
            'audit_logs.view',
        ];
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => $guard]);
        $staff->syncPermissions($staffPermissions);
    }
}
