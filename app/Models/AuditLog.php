<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasUuids;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'batch_id',
        'action_type',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Action constants (stored as "module.action" in action_type)
     */
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_CREATE = 'create';
    public const ACTION_EDIT = 'edit';
    public const ACTION_DELETE = 'delete';
    public const ACTION_UPLOAD = 'upload';
    public const ACTION_UPDATE_THEME = 'update_theme';
    public const ACTION_UPDATE_SETTINGS = 'update_settings';

    public const MODULE_AUTH = 'Auth';
    public const MODULE_PRODUCT = 'Product';
    public const MODULE_CATEGORY = 'Category';
    public const MODULE_USER = 'User';
    public const MODULE_ROLE = 'Role';
    public const MODULE_SITE_SETTING = 'Site Setting';
    public const MODULE_PAGE = 'Page';
    public const MODULE_IMAGE = 'Image/Asset';
    public const MODULE_THEME = 'Theme/Style';

    public static function actions(): array
    {
        return [
            self::ACTION_LOGIN => 'Login',
            self::ACTION_LOGOUT => 'Logout',
            self::ACTION_CREATE => 'Create',
            self::ACTION_EDIT => 'Edit',
            self::ACTION_DELETE => 'Delete',
            self::ACTION_UPLOAD => 'Upload',
            self::ACTION_UPDATE_THEME => 'Update Theme',
            self::ACTION_UPDATE_SETTINGS => 'Update Settings',
        ];
    }

    public static function modules(): array
    {
        return [
            self::MODULE_AUTH => 'Auth',
            self::MODULE_PRODUCT => 'Product',
            self::MODULE_CATEGORY => 'Category',
            self::MODULE_USER => 'User',
            self::MODULE_ROLE => 'Role',
            self::MODULE_SITE_SETTING => 'Site Setting',
            self::MODULE_PAGE => 'Page',
            self::MODULE_IMAGE => 'Image/Asset',
            self::MODULE_THEME => 'Theme/Style',
        ];
    }

    /** Get action part from action_type (e.g. "Auth.login" -> "login") */
    public function getActionAttribute(): string
    {
        if (!$this->action_type) {
            return '';
        }
        return str_contains($this->action_type, '.')
            ? substr($this->action_type, strrpos($this->action_type, '.') + 1)
            : $this->action_type;
    }

    /** Get module part from action_type (e.g. "Auth.login" -> "Auth") */
    public function getModuleAttribute(): string
    {
        if (!$this->action_type || !str_contains($this->action_type, '.')) {
            return '';
        }
        return substr($this->action_type, 0, strrpos($this->action_type, '.'));
    }

    public function getActionLabel(): string
    {
        return self::actions()[$this->action] ?? $this->action ?? $this->action_type ?? '';
    }

    public function getModuleLabel(): string
    {
        return self::modules()[$this->module] ?? $this->module ?? '';
    }

    /**
     * Get display label for a batch of logs (e.g. "Edit page: Home").
     */
    public static function getBatchGroupLabel(array $logs): string
    {
        if (empty($logs)) {
            return '—';
        }
        $first = $logs[0];
        $action = $first->getActionLabel();
        $module = $first->getModuleLabel();

        if ($module === 'Page' && $first->action === self::ACTION_EDIT) {
            $names = [];
            foreach ($logs as $log) {
                $obj = $log->object_changed;
                if ($obj) {
                    $base = preg_replace('/\s*\([^)]+\)\s*$/', '', $obj);
                    $names[$base] = true;
                }
            }
            $pageName = implode(', ', array_keys($names)) ?: 'Page';
            return "{$action} page: {$pageName}";
        }

        return $first->object_changed ?? "{$action} {$module}";
    }

    /** Get object_changed from new_value or old_value */
    public function getObjectChangedAttribute(): ?string
    {
        $val = $this->new_value ?? $this->old_value;
        if (is_array($val) && isset($val['object_changed'])) {
            return $val['object_changed'];
        }
        if (is_array($val) && isset($val['email'])) {
            return $val['email'];
        }
        return null;
    }

    /** Get details from new_value */
    public function getDetailsAttribute(): ?string
    {
        $val = $this->new_value ?? $this->old_value;
        if (is_array($val) && isset($val['details'])) {
            return $val['details'];
        }
        return null;
    }
}
