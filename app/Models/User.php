<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Mail\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasUuids, HasRoles, Notifiable;

    /**
     * Role constants
     */
    const ROLE_SYSTEM = 'system';
    const ROLE_ADMIN = 'admin';
    const ROLE_STAFF = 'staff';

    /**
     * Available roles
     */
    public static array $roles = [
        self::ROLE_SYSTEM => 'System',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_STAFF => 'Staff',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'api_token',
        'role',
        'status',
        'phone_number',
        'country_code',
        'location',
        'birth_date',
        'last_login_at',
        'login_count',
        'two_factor_enabled',
        'login_notifications',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'language',
        'timezone',
        'account_balance',
        'total_accumulated_funds',
        'current_level',
        'tier',
        'two_factor_secret',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'last_login_at' => 'datetime',
            'login_count' => 'integer',
            'birth_date' => 'date',
            'two_factor_enabled' => 'boolean',
            'login_notifications' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'language' => 'string',
            'timezone' => 'string',
            'country_code' => 'string',
            'account_balance' => 'decimal:2',
            'total_accumulated_funds' => 'decimal:2',
            'current_level' => 'integer',
            'tier' => 'string',
            'two_factor_secret' => 'string',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === false;
    }

    /**
     * Check if user is a system (super-admin)
     */
    public function isSystem(): bool
    {
        return $this->role === self::ROLE_SYSTEM;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    /**
     * Check if user has admin or higher privileges (system, admin - not staff)
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [self::ROLE_SYSTEM, self::ROLE_ADMIN]);
    }

    /**
     * Check if user has admin panel access (system, admin, staff)
     */
    public function hasAdminPanelAccess(): bool
    {
        return in_array($this->role, [self::ROLE_SYSTEM, self::ROLE_ADMIN, self::ROLE_STAFF]);
    }

    /**
     * Check if this user can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->isSystem();
    }

    /**
     * Check if this user's role can be changed
     */
    public function canChangeRole(): bool
    {
        return !$this->isSystem();
    }

    /**
     * Check if this user's status can be changed by the given user
     */
    public function canChangeStatus(?User $byUser = null): bool
    {
        // Cannot change own status
        if ($byUser && $this->id === $byUser->id) {
            return false;
        }
        return true;
    }

    /**
     * Get roles that can be assigned by the given user.
     * Fetches from the roles table so newly created roles appear in user create/edit forms.
     */
    public static function getAssignableRoles(?User $byUser = null): array
    {
        return Role::where('guard_name', 'web')
            ->where('name', '!=', self::ROLE_SYSTEM)
            ->orderByRaw("CASE name WHEN 'admin' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->mapWithKeys(fn (string $name) => [$name => ucfirst(str_replace('_', ' ', $name))])
            ->all();
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return self::$roles[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role ?? ''));
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Check if user has an avatar
     */
    public function hasAvatar(): bool
    {
        return !empty($this->avatar);
    }

    /**
     * Get avatar URL (returns default avatar if not set)
     */
    public function getAvatarUrl(): string
    {
        if ($this->hasAvatar()) {
            return url($this->avatar);
        }

        return url('images/avatars/default-avatar.webp');
    }

    /**
     * Check if custom avatar uploads are enabled
     */
    public function isCustomAvatarUploadEnabled(): bool
    {
        return \App\Models\SiteSetting::get('custom_avatar_upload_enabled', false);
    }

    /**
     * Get maximum avatar file size in KB
     */
    public function getMaxAvatarFileSize(): int
    {
        return \App\Models\SiteSetting::get('max_avatar_file_size', 2048); // Default 2MB
    }

    /**
     * Get all available avatars from gallery
     */
    public function getAvailableAvatars(): array
    {
        $avatars = \App\Models\SiteSetting::get('available_avatars', ['images/avatars/default-avatar.webp']);
        return array_map(function($avatar) {
            return [
                'url' => url($avatar),
                'path' => $avatar,
                'title' => basename($avatar)
            ];
        }, $avatars);
    }

    /**
     * Check if selected avatar is valid (exists in available gallery)
     */
    public function isValidAvatar(string $avatarPath): bool
    {
        $availableAvatars = \App\Models\SiteSetting::get('available_avatars', []);
        return in_array($avatarPath, $availableAvatars);
    }

    /**
     * Check if avatar is a custom uploaded file (not from gallery)
     */
    public function isCustomAvatar(): bool
    {
        if (!$this->hasAvatar()) {
            return false;
        }

        // If avatar path doesn't exist in available avatars, it's custom
        return !$this->isValidAvatar($this->avatar);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new ResetPasswordNotification($token, $this->email, $this->name));
    }

    public function login(Request $request)
    {
        $this->last_login_at = now();
        $this->login_count++;
        $this->save();
    }

    public function userGamePlays()
    {
        return $this->hasMany(UserGamePlay::class, 'user_id');
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class, 'user_id');
    }

    public function userFriends()
    {
        return $this->hasMany(UserFriend::class, 'user_id');
    }

    public function userDevices()
    {
        return $this->hasMany(UserDevice::class, 'user_id');
    }

    /**
     * Get full phone number with country code.
     */
    public function getFullPhoneNumberAttribute(): string
    {
        if (!$this->phone_number) {
            return '';
        }

        if ($this->country_code && !str_starts_with($this->phone_number, $this->country_code)) {
            return $this->country_code . ' ' . $this->phone_number;
        }

        return $this->phone_number;
    }

    /**
     * Add funds to total accumulated funds and update level
     *
     * @param float $amount
     * @return void
     */
    public function addAccumulatedFunds(float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $currentFunds = (float) ($this->total_accumulated_funds ?? 0);
        $newTotal = $currentFunds + $amount;
        $this->setAttribute('total_accumulated_funds', $newTotal);
    }
}
