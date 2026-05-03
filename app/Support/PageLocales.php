<?php

namespace App\Support;

class PageLocales
{
    public const SUPPORTED = ['en', 'id', 'th', 'vi', 'km', 'ms'];

    public const LABELS = [
        'en' => ['label' => 'EN', 'name' => 'English', 'flag' => '🇺🇸'],
        'id' => ['label' => 'ID', 'name' => 'Indonesian', 'flag' => '🇮🇩'],
        'th' => ['label' => 'TH', 'name' => 'Thai', 'flag' => '🇹🇭'],
        'vi' => ['label' => 'VN', 'name' => 'Vietnamese', 'flag' => '🇻🇳'],
        'km' => ['label' => 'KH', 'name' => 'Khmer', 'flag' => '🇰🇭'],
        'ms' => ['label' => 'MY', 'name' => 'Malay', 'flag' => '🇲🇾'],
    ];

    public static function all(): array
    {
        return self::SUPPORTED;
    }

    public static function labels(): array
    {
        return self::LABELS;
    }

    public static function isValid(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED, true);
    }

    public static function default(): string
    {
        return 'en';
    }
}
