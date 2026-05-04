<?php

namespace App\Support;

class PageLocales
{
    public const SUPPORTED = ['en', 'km', 'fr'];

    public const LABELS = [
        'en' => ['label' => 'EN', 'name' => 'English', 'flag' => '🇺🇸'],
        'km' => ['label' => 'KH', 'name' => 'Khmer', 'flag' => '🇰🇭'],
        'fr' => ['label' => 'FR', 'name' => 'French', 'flag' => '🇫🇷'],
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
