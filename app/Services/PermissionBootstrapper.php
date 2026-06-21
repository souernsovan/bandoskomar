<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionBootstrapper
{
    /**
     * Ensure permissions and role grants match config/permissions.php.
     *
     * This is idempotent and safe to call from both the seeder and the
     * admin middleware so production can self-heal if seeders were missed.
     */
    public function syncFromConfig(): void
    {
        $permissionGroups = config('permissions', []);

        $guard = 'web';
        $allPermissions = collect($permissionGroups)
            ->flatMap(fn ($permissions) => array_keys($permissions))
            ->unique()
            ->values()
            ->all();

        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission, guardName: $guard);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate([
            'name' => User::ROLE_ADMIN,
            'guard_name' => $guard,
        ]);

        $adminMissingPermissions = array_values(array_diff(
            $allPermissions,
            $adminRole->permissions()->pluck('name')->all()
        ));
        if ($adminMissingPermissions !== []) {
            $adminRole->givePermissionTo($adminMissingPermissions);
        }

        $staffRole = Role::firstOrCreate([
            'name' => User::ROLE_STAFF,
            'guard_name' => $guard,
        ]);

        $staffPermissions = [
            'products.view',
            'products.create',
            'products.edit',
            'users.view',
            'pages.view',
            'pages.edit',
            'audit_logs.view',
        ];

        $staffMissingPermissions = array_values(array_diff(
            $staffPermissions,
            $staffRole->permissions()->pluck('name')->all()
        ));
        if ($staffMissingPermissions !== []) {
            $staffRole->givePermissionTo($staffMissingPermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
