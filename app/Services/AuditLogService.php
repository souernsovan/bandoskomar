<?php

namespace App\Services;

use App\Helpers\IpHelper;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    public static function log(
        string $action,
        string $module,
        ?array $oldValue = null,
        ?array $newValue = null,
        ?Request $request = null
    ): ?AuditLog {
        try {
            $request = $request ?? request();
            $user = auth()->user();

            if (!$user) {
                return null;
            }

            $data = [
                'user_id' => $user->id,
                'action_type' => $module . '.' . $action,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'ip_address' => $request ? IpHelper::getClientIp($request) : (request() ? IpHelper::getClientIp(request()) : null),
                'user_agent' => $request?->userAgent(),
            ];
            if ($batchId = $request?->attributes->get('audit_batch_id')) {
                $data['batch_id'] = $batchId;
            }
            return AuditLog::create($data);
        } catch (\Throwable $e) {
            Log::error('AuditLogService::log failed', [
                'action' => $action,
                'module' => $module,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public static function logLogin(User $user, Request $request): void
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action_type' => AuditLog::MODULE_AUTH . '.' . AuditLog::ACTION_LOGIN,
            'old_value' => null,
            'new_value' => ['object_changed' => $user->email],
            'ip_address' => IpHelper::getClientIp($request),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public static function logLogout(?User $user, Request $request): void
    {
        if (!$user) {
            return;
        }
        AuditLog::create([
            'user_id' => $user->id,
            'action_type' => AuditLog::MODULE_AUTH . '.' . AuditLog::ACTION_LOGOUT,
            'old_value' => null,
            'new_value' => ['object_changed' => $user->email],
            'ip_address' => IpHelper::getClientIp($request),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public static function logCreate(string $module, string $objectChanged, ?array $newValue = null): void
    {
        $user = auth()->user();
        if (!$user) return;

        $data = $newValue ?? ['object_changed' => $objectChanged];
        $data['object_changed'] = $objectChanged;

        $createData = [
            'user_id' => $user->id,
            'action_type' => $module . '.' . AuditLog::ACTION_CREATE,
            'old_value' => null,
            'new_value' => $data,
            'ip_address' => request() ? IpHelper::getClientIp(request()) : null,
            'user_agent' => request()?->userAgent(),
        ];
        if ($batchId = request()?->attributes->get('audit_batch_id')) {
            $createData['batch_id'] = $batchId;
        }
        AuditLog::create($createData);
    }

    public static function logEdit(string $module, string $objectChanged, ?array $oldValue = null, ?array $newValue = null): void
    {
        $user = auth()->user();
        if (!$user) return;

        $old = $oldValue ?? [];
        $new = $newValue ?? ['object_changed' => $objectChanged];
        $new['object_changed'] = $objectChanged;

        // Only save fields that actually changed
        [$oldChanged, $newChanged] = self::getChangedFieldsOnly($old, $new);
        if (empty($oldChanged) && empty($newChanged)) {
            return; // No actual changes
        }
        $newChanged['object_changed'] = $objectChanged;

        $editData = [
            'user_id' => $user->id,
            'action_type' => $module . '.' . AuditLog::ACTION_EDIT,
            'old_value' => $oldChanged ?: null,
            'new_value' => $newChanged,
            'ip_address' => request() ? IpHelper::getClientIp(request()) : null,
            'user_agent' => request()?->userAgent(),
        ];
        if ($batchId = request()?->attributes->get('audit_batch_id')) {
            $editData['batch_id'] = $batchId;
        }
        AuditLog::create($editData);
    }

    /**
     * Compare old and new arrays, return only the fields that changed.
     * For translations and page_content, only includes locales/fields that actually changed.
     * Returns [oldChanged, newChanged].
     */
    private static function getChangedFieldsOnly(array $old, array $new): array
    {
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
        $oldChanged = [];
        $newChanged = [];

        foreach ($allKeys as $key) {
            if ($key === 'object_changed') {
                continue;
            }

            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            // Deep diff for translations: only log locales/fields that changed
            if ($key === 'translations' && (is_array($oldVal) || is_array($newVal))) {
                [$diffOld, $diffNew] = self::diffTranslations($oldVal ?? [], $newVal ?? []);
                if (!empty($diffOld) || !empty($diffNew)) {
                    $oldChanged[$key] = $diffOld ?: null;
                    $newChanged[$key] = $diffNew;
                }
                continue;
            }

            // Deep diff for page_content: only log locales/fields that changed
            if ($key === 'page_content' && (is_array($oldVal) || is_array($newVal))) {
                [$diffOld, $diffNew] = self::diffPageContent($oldVal ?? [], $newVal ?? []);
                if (!empty($diffOld) || !empty($diffNew)) {
                    $oldChanged[$key] = $diffOld ?: null;
                    $newChanged[$key] = $diffNew;
                }
                continue;
            }

            // Permissions: only log added/removed instead of full arrays
            if ($key === 'permissions' && is_array($oldVal) && is_array($newVal)) {
                $oldSet = array_values(array_unique($oldVal));
                $newSet = array_values(array_unique($newVal));
                sort($oldSet);
                sort($newSet);
                if ($oldSet !== $newSet) {
                    $removed = array_values(array_diff($oldSet, $newSet));
                    $added = array_values(array_diff($newSet, $oldSet));
                    $oldChanged[$key] = ['removed' => $removed];
                    $newChanged[$key] = ['added' => $added];
                }
                continue;
            }

            if (is_array($oldVal) || is_array($newVal)) {
                $oldStr = json_encode(self::normalizeForComparison($oldVal));
                $newStr = json_encode(self::normalizeForComparison($newVal));
                if ($oldStr !== $newStr) {
                    $oldChanged[$key] = $oldVal;
                    $newChanged[$key] = $newVal;
                }
            } elseif ((string) ($oldVal ?? '') !== (string) ($newVal ?? '')) {
                $oldChanged[$key] = $oldVal;
                $newChanged[$key] = $newVal;
            }
        }

        return [$oldChanged, $newChanged];
    }

    /**
     * Diff translations: only return locales where title or content changed.
     * Returns [oldChanged, newChanged].
     */
    public static function diffTranslations(array $old, array $new): array
    {
        if (json_encode(self::normalizeForComparison($old)) === json_encode(self::normalizeForComparison($new))) {
            return [[], []];
        }

        $allLocales = array_unique(array_merge(array_keys($old), array_keys($new)));
        $oldChanged = [];
        $newChanged = [];

        foreach ($allLocales as $locale) {
            $oldLocale = $old[$locale] ?? [];
            $newLocale = $new[$locale] ?? [];
            $oldTitle = $oldLocale['title'] ?? null;
            $newTitle = $newLocale['title'] ?? null;
            $oldContent = $oldLocale['content'] ?? null;
            $newContent = $newLocale['content'] ?? null;

            $titleChanged = (string) $oldTitle !== (string) $newTitle;
            $contentChanged = (string) $oldContent !== (string) $newContent;

            if (!$titleChanged && !$contentChanged) {
                continue;
            }

            $oldOut = [];
            $newOut = [];
            if ($titleChanged) {
                $oldOut['title'] = $oldTitle;
                $newOut['title'] = $newTitle;
            }
            if ($contentChanged) {
                $oldOut['content'] = $oldContent;
                $newOut['content'] = $newContent;
            }
            $oldChanged[$locale] = $oldOut;
            $newChanged[$locale] = $newOut;
        }

        return [$oldChanged, $newChanged];
    }

    /** Fields shared across all locales (same value for every language) */
    private static array $sharedImageKeys = [
        'hero_image', 'company_logo', 'capabilities_image', 'marketing_image', 'mobile_image', 'mobile_bg',
        'partner_images', 'platform_slider_images', 'platform_image', 'choose_col_1_image', 'different_image', 'promise_image',
        'styles',
    ];

    /**
     * Diff page_content: only return locales where any field changed.
     * Shared images (hero, marketing, etc.) identical across all locales
     * are logged once as "global" instead of per-locale.
     */
    public static function diffPageContent(array $old, array $new): array
    {
        if (json_encode(self::normalizeForComparison($old)) === json_encode(self::normalizeForComparison($new))) {
            return [[], []];
        }

        $locales = \App\Support\PageLocales::all();
        $isOldLocaleKeyed = self::isLocaleKeyed($old, $locales);
        $isNewLocaleKeyed = self::isLocaleKeyed($new, $locales);

        if ($isOldLocaleKeyed || $isNewLocaleKeyed) {
            $oldByLocale = $isOldLocaleKeyed ? $old : ['en' => $old];
            $newByLocale = $isNewLocaleKeyed ? $new : ['en' => $new];
            $allLocales = array_unique(array_merge(array_keys($oldByLocale), array_keys($newByLocale)));
            $allLocales = array_values(array_filter($allLocales, fn ($l) => in_array($l, $locales, true)));
            $oldChanged = [];
            $newChanged = [];

            foreach ($allLocales as $locale) {
                $oldLocale = $oldByLocale[$locale] ?? [];
                $newLocale = $newByLocale[$locale] ?? [];
                [$diffOld, $diffNew] = self::diffFlatContent((array) $oldLocale, (array) $newLocale);
                if (!empty($diffOld) || !empty($diffNew)) {
                    $oldChanged[$locale] = $diffOld;
                    $newChanged[$locale] = $diffNew;
                }
            }
            [$oldChanged, $newChanged] = self::collapseSharedImagesToGlobal($oldByLocale, $newByLocale, $oldChanged, $newChanged);

            // Strip locale/global entries where all values are null or empty
            foreach ([&$oldChanged, &$newChanged] as &$changed) {
                foreach ($changed as $locale => $data) {
                    if (is_array($data)) {
                        $hasValue = false;
                        foreach ($data as $v) {
                            if ($v !== null && $v !== '' && !(is_array($v) && empty($v))) {
                                $hasValue = true;
                                break;
                            }
                        }
                        if (!$hasValue) {
                            unset($changed[$locale]);
                        }
                    }
                }
            }
            unset($changed);

            return [$oldChanged, $newChanged];
        }

        return self::diffFlatContent($old, $new);
    }

    /**
     * Shared image fields that changed identically in all locales → log once as _global.
     */
    private static function collapseSharedImagesToGlobal(array $oldByLocale, array $newByLocale, array $oldChanged, array $newChanged): array
    {
        $globalOld = [];
        $globalNew = [];

        foreach (self::$sharedImageKeys as $key) {
            $oldVals = [];
            $newVals = [];
            $serializeVal = function ($data, $key) {
                if (!is_array($data) || !array_key_exists($key, $data)) {
                    return '__absent';
                }
                $v = $data[$key];
                if ($v === null || $v === '' || (is_array($v) && empty($v))) {
                    return '__empty';
                }
                return is_array($v) ? json_encode(self::normalizeForComparison($v)) : trim((string) $v);
            };
            foreach ($oldByLocale as $locale => $data) {
                if (in_array($locale, \App\Support\PageLocales::all(), true)) {
                    $oldVals[] = $serializeVal($data, $key);
                }
            }
            foreach ($newByLocale as $locale => $data) {
                if (in_array($locale, \App\Support\PageLocales::all(), true)) {
                    $newVals[] = $serializeVal($data, $key);
                }
            }
            if (empty($oldVals) && empty($newVals)) {
                continue;
            }
            $uniqueOld = array_unique($oldVals);
            $uniqueNew = array_unique($newVals);
            if (count($uniqueOld) === 1 && count($uniqueNew) === 1) {
                $firstOld = reset($oldByLocale);
                $firstNew = reset($newByLocale);
                $oVal = is_array($firstOld) ? ($firstOld[$key] ?? null) : null;
                $nVal = is_array($firstNew) ? ($firstNew[$key] ?? null) : null;

                // Treat null, empty array, and empty string as equivalent (no real change)
                $oEmpty = $oVal === null || $oVal === '' || (is_array($oVal) && empty($oVal));
                $nEmpty = $nVal === null || $nVal === '' || (is_array($nVal) && empty($nVal));
                if ($oEmpty && $nEmpty) {
                    continue;
                }

                $oStr = is_array($oVal) ? json_encode(self::normalizeForComparison($oVal)) : trim((string) ($oVal ?? ''));
                $nStr = is_array($nVal) ? json_encode(self::normalizeForComparison($nVal)) : trim((string) ($nVal ?? ''));
                if ($oStr !== $nStr) {
                    // For 'styles', use the per-style diff already computed by diffFlatContent
                    // instead of the full raw arrays, so only changed styles are logged.
                    if ($key === 'styles') {
                        $diffedOld = null;
                        $diffedNew = null;
                        foreach ($oldChanged as $loc => $data) {
                            if ($loc !== '_global' && array_key_exists($key, $data)) {
                                $diffedOld = $data[$key];
                                break;
                            }
                        }
                        foreach ($newChanged as $loc => $data) {
                            if ($loc !== '_global' && array_key_exists($key, $data)) {
                                $diffedNew = $data[$key];
                                break;
                            }
                        }
                        if ($diffedOld === null && $diffedNew === null) {
                            continue;
                        }
                        $globalOld[$key] = $diffedOld;
                        $globalNew[$key] = $diffedNew;
                    } else {
                        $globalOld[$key] = $oVal;
                        $globalNew[$key] = $nVal;
                    }
                    foreach (array_keys($oldChanged) as $locale) {
                        if ($locale === '_global') {
                            continue;
                        }
                        unset($oldChanged[$locale][$key], $newChanged[$locale][$key]);
                    }
                    foreach ($oldChanged as $locale => $data) {
                        if ($locale !== '_global' && empty($data)) {
                            unset($oldChanged[$locale]);
                        }
                    }
                    foreach ($newChanged as $locale => $data) {
                        if ($locale !== '_global' && empty($data)) {
                            unset($newChanged[$locale]);
                        }
                    }
                }
            }
        }

        if (!empty($globalOld) || !empty($globalNew)) {
            $oldChanged['_global'] = array_merge($oldChanged['_global'] ?? [], $globalOld);
            $newChanged['_global'] = array_merge($newChanged['_global'] ?? [], $globalNew);
        }

        return [$oldChanged, $newChanged];
    }

    private static function isLocaleKeyed(array $arr, array $locales): bool
    {
        foreach (array_keys($arr) as $key) {
            if (in_array($key, $locales, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Diff flat key-value content, return only changed keys.
     * Handles arrays (features, solution_cards, etc.) by full comparison.
     * Special handling for 'styles': diffs per-style so unchanged styles are omitted.
     */
    private static function diffFlatContent(array $old, array $new): array
    {
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
        $oldChanged = [];
        $newChanged = [];

        foreach ($allKeys as $key) {
            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            // Treat null, empty array, and empty string as equivalent (no real change)
            $oEmpty = $oldVal === null || $oldVal === '' || (is_array($oldVal) && empty($oldVal));
            $nEmpty = $newVal === null || $newVal === '' || (is_array($newVal) && empty($newVal));
            if ($oEmpty && $nEmpty) {
                continue;
            }

            if ($key === 'styles') {
                [$diffOld, $diffNew] = self::diffStylesArray(
                    is_array($oldVal) ? $oldVal : [],
                    is_array($newVal) ? $newVal : []
                );
                if (!empty($diffOld) || !empty($diffNew)) {
                    $oldChanged[$key] = $diffOld;
                    $newChanged[$key] = $diffNew;
                }
                continue;
            }

            if (is_array($oldVal) || is_array($newVal)) {
                $oldStr = json_encode(self::normalizeForComparison($oldVal));
                $newStr = json_encode(self::normalizeForComparison($newVal));
                if ($oldStr !== $newStr) {
                    $oldChanged[$key] = $oldVal;
                    $newChanged[$key] = $newVal;
                }
            } elseif ((string) ($oldVal ?? '') !== (string) ($newVal ?? '')) {
                $oldChanged[$key] = $oldVal;
                $newChanged[$key] = $newVal;
            }
        }

        return [$oldChanged, $newChanged];
    }

    /**
     * Recursively normalize values for comparison: null → '', trim strings.
     * Used to avoid false diffs from null vs '' differences in nested data.
     */
    private static function normalizeForComparison($value): mixed
    {
        if ($value === null) {
            return '';
        }
        if (is_string($value)) {
            return trim($value);
        }
        if (is_array($value)) {
            $normalized = array_map([self::class, 'normalizeForComparison'], $value);
            if (!empty($normalized) && !array_is_list($normalized)) {
                ksort($normalized);
            }
            return $normalized;
        }
        return $value;
    }

    /**
     * Per-style diff with field-level granularity.
     * Only includes styles that changed, and within each style only the
     * specific fields (image, individual colors) that actually changed.
     */
    private static function diffStylesArray(array $oldStyles, array $newStyles): array
    {
        $maxIndex = max(count($oldStyles), count($newStyles));
        if ($maxIndex === 0) {
            return [[], []];
        }

        $oldChanged = [];
        $newChanged = [];

        for ($i = 0; $i < $maxIndex; $i++) {
            $oldStyle = $oldStyles[$i] ?? null;
            $newStyle = $newStyles[$i] ?? null;

            // Added or removed entire style
            if ($oldStyle === null || $newStyle === null) {
                if ($oldStyle !== null || $newStyle !== null) {
                    $oldChanged[$i] = $oldStyle;
                    $newChanged[$i] = $newStyle;
                }
                continue;
            }

            $oldNorm = self::normalizeStyle($oldStyle);
            $newNorm = self::normalizeStyle($newStyle);

            if (json_encode($oldNorm) === json_encode($newNorm)) {
                continue;
            }

            // Diff the image field
            $oldPart = [];
            $newPart = [];
            if ($oldNorm['image'] !== $newNorm['image']) {
                $oldPart['image'] = $oldStyle['image'] ?? '';
                $newPart['image'] = $newStyle['image'] ?? '';
            }

            // Diff colors individually
            $oldColors = array_values($oldNorm['colors']);
            $newColors = array_values($newNorm['colors']);
            $maxC = max(count($oldColors), count($newColors));
            $oldColorDiff = [];
            $newColorDiff = [];
            for ($c = 0; $c < $maxC; $c++) {
                $oc = $oldColors[$c] ?? null;
                $nc = $newColors[$c] ?? null;
                if (json_encode($oc) !== json_encode($nc)) {
                    $rawOldColors = array_values(is_array($oldStyle['colors'] ?? null) ? $oldStyle['colors'] : []);
                    $rawNewColors = array_values(is_array($newStyle['colors'] ?? null) ? $newStyle['colors'] : []);
                    $oldColorDiff[$c] = $rawOldColors[$c] ?? null;
                    $newColorDiff[$c] = $rawNewColors[$c] ?? null;
                }
            }
            if (!empty($oldColorDiff) || !empty($newColorDiff)) {
                $oldPart['colors'] = $oldColorDiff;
                $newPart['colors'] = $newColorDiff;
            }

            if (!empty($oldPart) || !empty($newPart)) {
                $oldChanged[$i] = $oldPart;
                $newChanged[$i] = $newPart;
            }
        }

        return [$oldChanged, $newChanged];
    }

    /**
     * Normalize a style entry for comparison: lowercase hex, trim strings,
     * normalize empty images to '', and re-index colors sequentially.
     */
    private static function normalizeStyle($style): array
    {
        if (!is_array($style)) {
            return [];
        }
        $colors = [];
        foreach (array_values($style['colors'] ?? []) as $color) {
            if (!is_array($color)) {
                continue;
            }
            $colors[] = [
                'name'  => trim($color['name'] ?? ''),
                'hex'   => strtolower(trim($color['hex'] ?? '')),
                'image' => trim((string) ($color['image'] ?? '')),
            ];
        }
        return [
            'image'  => trim((string) ($style['image'] ?? '')),
            'colors' => $colors,
        ];
    }

    public static function logDelete(string $module, string $objectChanged): void
    {
        $user = auth()->user();
        if (!$user) return;

        $deleteData = [
            'user_id' => $user->id,
            'action_type' => $module . '.' . AuditLog::ACTION_DELETE,
            'old_value' => null,
            'new_value' => ['object_changed' => $objectChanged],
            'ip_address' => request() ? IpHelper::getClientIp(request()) : null,
            'user_agent' => request()?->userAgent(),
        ];
        if ($batchId = request()?->attributes->get('audit_batch_id')) {
            $deleteData['batch_id'] = $batchId;
        }
        AuditLog::create($deleteData);
    }

    public static function logUpload(string $objectChanged, ?array $newValue = null): void
    {
        $user = auth()->user();
        if (!$user) return;

        $data = $newValue ?? ['object_changed' => $objectChanged];
        $data['object_changed'] = $objectChanged;

        AuditLog::create([
            'user_id' => $user->id,
            'action_type' => AuditLog::MODULE_IMAGE . '.' . AuditLog::ACTION_UPLOAD,
            'old_value' => null,
            'new_value' => $data,
            'ip_address' => request() ? IpHelper::getClientIp(request()) : null,
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public static function logThemeUpdate(string $theme): void
    {
        $user = auth()->user();
        if (!$user) return;

        AuditLog::create([
            'user_id' => $user->id,
            'action_type' => AuditLog::MODULE_THEME . '.' . AuditLog::ACTION_UPDATE_THEME,
            'old_value' => null,
            'new_value' => [
                'object_changed' => $theme,
                'theme' => $theme,
            ],
            'ip_address' => request() ? IpHelper::getClientIp(request()) : null,
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public static function logSettingsUpdate(string $key, ?array $oldValue = null, ?array $newValue = null): void
    {
        $user = auth()->user();
        if (!$user) return;

        $new = $newValue ?? ['object_changed' => $key, 'key' => $key];

        AuditLog::create([
            'user_id' => $user->id,
            'action_type' => AuditLog::MODULE_SITE_SETTING . '.' . AuditLog::ACTION_UPDATE_SETTINGS,
            'old_value' => $oldValue,
            'new_value' => $new,
            'ip_address' => request() ? IpHelper::getClientIp(request()) : null,
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
