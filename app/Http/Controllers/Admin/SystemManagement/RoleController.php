<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Require roles.edit permission (used for create, store, edit, update).
     */
    private function authorizeEdit(): void
    {
        if (!auth()->user()->can('roles.edit')) {
            abort(403, 'You do not have permission to manage roles and permissions.');
        }
    }

    /**
     * Display list of roles (excludes system).
     */
    public function index()
    {
        $roles = Role::where('guard_name', 'web')
            ->where('name', '!=', 'system')
            ->orderByRaw("CASE name WHEN 'admin' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->get();

        return view('admin.system-management.roles.index', compact('roles'));
    }

    /**
     * Display a single role and its permissions.
     */
    public function show(string $roleName)
    {
        $role = Role::where('guard_name', 'web')->where('name', $roleName)->firstOrFail();
        $permissionGroups = config('permissions', []);
        $rolePermissionNames = $role->permissions->pluck('name')->toArray();

        return view('admin.system-management.roles.show', compact('role', 'permissionGroups', 'rolePermissionNames'));
    }

    /**
     * Show form to create a new role.
     */
    public function create()
    {
        $this->authorizeEdit();

        $permissionGroups = config('permissions', []);
        $rolePermissions = [];

        return view('admin.system-management.roles.create', compact('permissionGroups', 'rolePermissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorizeEdit();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', Rule::unique('roles')->where('guard_name', 'web')],
        ], [
            'name.unique' => 'A role with this name already exists.',
            'name.regex' => 'Role name must contain only lowercase letters, numbers, and underscores.',
        ]);

        $validated['guard_name'] = 'web';
        $validated['name'] = strtolower($validated['name']);

        $role = Role::create($validated);

        $validPermissions = $this->getValidPermissionKeys();
        $requestedPermissions = $request->input('permissions', []);
        $permissions = array_filter($requestedPermissions, fn ($p) => in_array($p, $validPermissions));
        $role->syncPermissions($permissions);

        $newData = [
            'name' => $role->name,
            'permissions' => array_values($permissions),
        ];
        AuditLogService::logCreate(AuditLog::MODULE_ROLE, $role->name, $newData);

        return redirect()
            ->route('system-management.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Show form to edit role permissions.
     */
    public function edit(string $roleName)
    {
        $this->authorizeEdit();

        $role = Role::where('guard_name', 'web')->where('name', $roleName)->firstOrFail();

        $permissionGroups = config('permissions', []);
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.system-management.roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    /**
     * Update role permissions.
     */
    public function update(Request $request, string $roleName)
    {
        $this->authorizeEdit();

        $role = Role::where('guard_name', 'web')->where('name', $roleName)->firstOrFail();

        $oldData = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ];

        $validPermissions = $this->getValidPermissionKeys();
        $requestedPermissions = $request->input('permissions', []);
        $permissions = array_filter($requestedPermissions, fn ($p) => in_array($p, $validPermissions));
        $role->syncPermissions($permissions);

        User::where('role', $role->name)->each(fn (User $u) => $u->syncRoles([$role->name]));

        $newData = [
            'name' => $role->name,
            'permissions' => array_values($permissions),
        ];
        AuditLogService::logEdit(AuditLog::MODULE_ROLE, $role->name, $oldData, $newData);

        return redirect()
            ->route('system-management.roles.index')
            ->with('success', "Permissions for role '{$role->name}' updated successfully.");
    }

    /**
     * Get all valid permission keys from config.
     */
    private function getValidPermissionKeys(): array
    {
        $keys = [];
        foreach (config('permissions', []) as $group) {
            $keys = array_merge($keys, array_keys($group));
        }
        return $keys;
    }
}
