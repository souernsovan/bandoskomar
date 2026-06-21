<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminPagesRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_products_page_bootstraps_permissions_and_loads(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('Programs');

        $this->assertDatabaseHas('permissions', [
            'name' => 'products.view',
            'guard_name' => 'web',
        ]);

        $this->assertTrue(
            Role::findByName(User::ROLE_ADMIN, 'web')->hasPermissionTo('products.view')
        );
    }

    public function test_admin_audit_logs_page_loads_with_batched_entries(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => true,
        ]);

        $batchId = (string) Str::uuid();

        AuditLog::create([
            'user_id' => $admin->id,
            'batch_id' => $batchId,
            'action_type' => AuditLog::MODULE_PRODUCT . '.' . AuditLog::ACTION_EDIT,
            'old_value' => ['object_changed' => 'Program Alpha'],
            'new_value' => ['object_changed' => 'Program Alpha Updated'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'batch_id' => $batchId,
            'action_type' => AuditLog::MODULE_PRODUCT . '.' . AuditLog::ACTION_EDIT,
            'old_value' => ['object_changed' => 'Program Beta'],
            'new_value' => ['object_changed' => 'Program Beta Updated'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $this->actingAs($admin)
            ->get('/admin/audit-logs')
            ->assertOk()
            ->assertSee('Audit Log')
            ->assertSee('Program Alpha');
    }
}
