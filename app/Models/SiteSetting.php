<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    use HasFactory, HasUuids;

    public const DEFAULT_SITE_LOGO = 'images/logo/community-care-logo.svg';

    public const DEFAULT_SITE_ICON = 'images/logo/community-care-icon.svg';

    /** Uploaded branding files are stored under this public path prefix */
    public const BRANDING_UPLOAD_PREFIX = 'images/site/branding/';

    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function getGroupDisplayName(): string
    {
        return match ($this->group) {
            'general' => 'General',
            'contact' => 'Contact',
            'social' => 'Social Media',
            'analytics' => 'Analytics',
            'seo' => 'SEO',
            'footer' => 'Footer',
            'global' => 'Global',
            'tools' => 'Tools',
            'other' => 'Other',
            default => ucfirst($this->group ?? 'Unknown'),
        };
    }

    public function getGroupBadgeClass(): string
    {
        return match ($this->group) {
            'general' => 'blue',
            'contact' => 'indigo',
            'social' => 'indigo',
            'analytics' => 'yellow',
            'seo' => 'red',
            'footer' => 'gray',
            'global' => 'blue',
            'tools' => 'blue',
            default => 'secondary',
        };
    }

    public function getValueDisplayName(): string
    {
        if (is_array($this->value)) {
            return 'Array data ('.count($this->value).' items)';
        }

        return Str::limit($this->value ?? '', 50, '...');
    }

    /**
     * Get a site setting value by key
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a site setting value
     *
     * @param  mixed  $value
     */
    public static function set(string $key, $value, string $group = 'general'): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
            ]
        );
    }

    /**
     * Get all settings for a specific group
     */
    public static function getGroup(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->get()->pluck('value', 'key');
    }

    /**
     * Public relative path (under public/) for the site logo image.
     */
    public static function siteLogoPath(): string
    {
        $v = static::get('site_logo');

        return is_string($v) && $v !== '' ? $v : self::DEFAULT_SITE_LOGO;
    }

    /**
     * Public relative path (under public/) for the favicon / site icon.
     */
    public static function siteIconPath(): string
    {
        $v = static::get('site_icon');

        return is_string($v) && $v !== '' ? $v : self::DEFAULT_SITE_ICON;
    }

    /**
     * MIME type hint for the favicon link tag.
     */
    public static function siteIconMimeType(): string
    {
        $ext = strtolower(pathinfo(static::siteIconPath(), PATHINFO_EXTENSION));

        return match ($ext) {
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'image/x-icon',
        };
    }

    public static function isManagedBrandingPath(?string $path): bool
    {
        return is_string($path) && str_starts_with($path, self::BRANDING_UPLOAD_PREFIX);
    }
}
