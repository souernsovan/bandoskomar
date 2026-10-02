<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Read-only access to a page's editable content for the current locale.
 *
 * Lookup order for text: current language → language default → English → schema default.
 * Pictures, links, numbers and list items always come from English (see ContentSchema).
 */
class PageContent
{
    private array $fields;

    public function __construct(
        private string $slug,
        private array $base,
        private array $translated,
        private string $locale,
    ) {
        $this->fields = ContentSchema::fields($slug);
    }

    public static function forSlug(string $slug, ?Page $page = null): self
    {
        $locale = app()->getLocale();
        // Cached on the current request, so every request reads fresh content.
        $cache = request()->attributes;
        $cacheKey = 'page-content.'.$slug.'|'.$locale;

        if (! $cache->has($cacheKey)) {
            $page ??= Page::where('slug', $slug)->first();
            $content = self::localeKeyed($page?->page_content ?? []);
            $default = PageLocales::default();
            $cache->set($cacheKey, new self(
                $slug,
                $content[$default] ?? [],
                $locale !== $default ? ($content[$locale] ?? []) : [],
                $locale,
            ));
        }

        return $cache->get($cacheKey);
    }

    /** Content of the hidden "site" page (header, footer, popup…). */
    public static function site(): self
    {
        return self::forSlug(ContentSchema::SITE);
    }

    /** Raw value (string, number, bool). */
    public function get(string $key): mixed
    {
        $field = $this->fields[$key] ?? ['type' => 'text', 'default' => ''];

        if (ContentSchema::isTranslatable($field) && $this->locale !== PageLocales::default()) {
            $value = $this->translated[$key] ?? '';
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
            if (isset($field['default_'.$this->locale])) {
                return $field['default_'.$this->locale];
            }
        }

        return array_key_exists($key, $this->base) ? $this->base[$key] : ($field['default'] ?? '');
    }

    public function has(string $key): bool
    {
        $value = $this->get($key);

        return is_string($value) ? trim($value) !== '' : ! empty($value);
    }

    /** Text field that may contain a few safe HTML tags. */
    public function html(string $key): HtmlString
    {
        return self::clean((string) $this->get($key));
    }

    /** Textarea split into paragraphs (one per non-empty line). */
    public function paragraphs(string $key): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", (string) $this->get($key))), 'strlen'));
    }

    /** Public URL for an image/file field. */
    public function media(string $key): string
    {
        return self::url((string) $this->get($key));
    }

    /** Link for a url field. */
    public function link(string $key): string
    {
        return self::href((string) $this->get($key));
    }

    /**
     * Items of a list field, with translated text merged in item by item.
     *
     * @return array<int, array<string, mixed>>
     */
    public function list(string $key): array
    {
        $field = $this->fields[$key] ?? ['default' => [], 'fields' => []];
        $items = array_key_exists($key, $this->base) && is_array($this->base[$key])
            ? $this->base[$key]
            : ($field['default'] ?? []);
        $items = array_values(array_filter($items, 'is_array'));

        $translations = $this->locale !== PageLocales::default() && is_array($this->translated[$key] ?? null)
            ? array_values($this->translated[$key])
            : [];

        foreach ($items as $i => $item) {
            foreach ($field['fields'] ?? [] as $subKey => $sub) {
                $item[$subKey] ??= $sub['default'] ?? '';
                $tr = $translations[$i][$subKey] ?? '';
                if (ContentSchema::isTranslatable($sub) && is_string($tr) && trim($tr) !== '') {
                    $item[$subKey] = $tr;
                }
            }
            $items[$i] = $item;
        }

        return $items;
    }

    // ------------------------------------------------------------------
    // Helpers usable from views
    // ------------------------------------------------------------------

    /** Turn a stored path or external URL into a public URL. */
    public static function url(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if (preg_match('#^(https?:)?//#i', $value) || Str::startsWith($value, 'data:')) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    /** URL safe to place inside CSS url('…') in a style attribute. */
    public static function cssUrl(?string $value): string
    {
        return str_replace(["'", '"', '(', ')', ' '], ['%27', '%22', '%28', '%29', '%20'], self::url($value));
    }

    /** "$25", "$1,250", "$7.50". */
    public static function money(mixed $amount): string
    {
        $amount = (float) $amount;

        return '$'.number_format($amount, floor($amount) === $amount ? 0 : 2);
    }

    /** Turn a link field into an href. */
    public static function href(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '#';
        }
        if (preg_match('#^([a-z][a-z0-9+.-]*:|//|\#)#i', $value)) {
            return preg_match('#^javascript:#i', $value) ? '#' : $value;
        }

        return url('/'.ltrim($value, '/'));
    }

    /** Allow only simple inline tags in admin-written text. */
    public static function clean(string $html): HtmlString
    {
        $html = strip_tags($html, '<mark><strong><b><em><i><br><a><span>');
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href\s*=\s*["\']?)\s*javascript:/i', '$1#', $html);

        return new HtmlString($html);
    }

    /** Convert a YouTube/Vimeo page link into an embeddable player URL. */
    public static function embedUrl(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{6,})~i', $value, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $value, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return $value;
    }

    /** Two-letter initials for avatars. */
    public static function initials(string $name): string
    {
        $words = preg_split('/\s+/u', trim($name)) ?: [];
        $letters = array_map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)), array_slice($words, 0, 2));

        return implode('', $letters);
    }

    private static function localeKeyed(array $content): array
    {
        foreach (array_keys($content) as $key) {
            if (in_array($key, PageLocales::all(), true)) {
                return $content;
            }
        }

        return [PageLocales::default() => $content];
    }
}
