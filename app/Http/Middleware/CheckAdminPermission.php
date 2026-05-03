<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    /**
     * Route name to permission mapping.
     */
    protected array $routePermissions = [
        'admin.products.index' => 'products.view',
        'admin.products.show' => 'products.view',
        'admin.products.create' => 'products.create',
        'admin.products.store' => 'products.create',
        'admin.products.edit' => 'products.edit',
        'admin.products.update' => 'products.edit',
        'admin.products.destroy' => 'products.delete',
        'system-management.categories.index' => 'categories.view',
        'system-management.categories.show' => 'categories.view',
        'system-management.categories.create' => 'categories.create',
        'system-management.categories.store' => 'categories.create',
        'system-management.categories.edit' => 'categories.edit',
        'system-management.categories.update' => 'categories.edit',
        'system-management.categories.destroy' => 'categories.delete',
        'system-management.users.index' => 'users.view',
        'system-management.users.show' => 'users.view',
        'system-management.users.create' => 'users.create',
        'system-management.users.store' => 'users.create',
        'system-management.users.edit' => 'users.edit',
        'system-management.users.update' => 'users.edit',
        'system-management.users.destroy' => 'users.delete',
        'system-management.site-settings.index' => 'site_settings.view',
        'system-management.site-settings.show' => 'site_settings.view',
        'system-management.site-settings.create' => 'site_settings.create',
        'system-management.site-settings.store' => 'site_settings.create',
        'system-management.site-settings.edit' => 'site_settings.edit',
        'system-management.site-settings.update' => 'site_settings.edit',
        'system-management.pages.index' => 'pages.view',
        'system-management.pages.show' => 'pages.view',
        'system-management.pages.create' => 'pages.edit',
        'system-management.pages.store' => 'pages.edit',
        'system-management.pages.edit' => 'pages.edit',
        'system-management.pages.update' => 'pages.edit',
        'system-management.pages.destroy' => 'pages.edit',
        'system-management.roles.index' => 'roles.view',
        'system-management.roles.show' => 'roles.view',
        'system-management.roles.create' => 'roles.edit',
        'system-management.roles.store' => 'roles.edit',
        'system-management.roles.edit' => 'roles.edit',
        'system-management.roles.update' => 'roles.edit',
        'system-management.audit-logs.index' => 'audit_logs.view',
        'system-management.audit-logs.show' => 'audit_logs.view',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // System always has full access
        if ($user && $user->isSystem()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $permission = $this->routePermissions[$routeName] ?? null;

        if (!$permission) {
            return $next($request);
        }

        if (!$user || !$user->can($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
