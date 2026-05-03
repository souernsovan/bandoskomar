<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteSettingController extends Controller
{
    /**
     * Display a listing of site settings.
     */
    public function index(Request $request)
    {
        $query = SiteSetting::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%");
            });
        }

        // Filter by group
        if ($request->has('group') && $request->group) {
            $group = $request->group;
            $query->where('group', $group);
        }

        // Order by created_at descending
        $query->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $siteSettings = $query->paginate($perPage);

        return view('admin.system-management.site-settings.index', compact('siteSettings', 'perPage'));
    }

    /**
     * Show the form for creating a new site setting.
     */
    public function create()
    {
        return view('admin.system-management.site-settings.create');
    }

    /**
     * Store a newly created site setting.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:site_settings,key',
            'value' => 'required',
            'group' => 'required|string|max:255|in:general,contact,social,analytics,seo,footer,global,tools,other',
        ]);

        // Handle JSON values - try to decode if it's a JSON string
        $value = $request->input('value');

        // Check if the value is a JSON string
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            // If JSON decode was successful and result is an array or object, use decoded value
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                $validated['value'] = $decoded;
            } else {
                // If it's not valid JSON, treat as regular string
                // But check length to prevent extremely long strings
                if (strlen($value) > 10000) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['value' => 'Value is too long. Maximum 10000 characters allowed.']);
                }
                $validated['value'] = $value;
            }
        } else {
            // If value is already an array/object, use it directly
            $validated['value'] = $value;
        }

        $siteSetting = SiteSetting::create($validated);

        $newData = [
            'key' => $siteSetting->key,
            'group' => $siteSetting->group,
            'value' => $this->formatValueForAudit($siteSetting->value),
        ];
        AuditLogService::logCreate(AuditLog::MODULE_SITE_SETTING, $siteSetting->key, $newData);

        return redirect()->route('system-management.site-settings.index')->with('success', 'Site setting created successfully');
    }

    /**
     * Display the specified site setting.
     */
    public function show(SiteSetting $siteSetting)
    {
        return view('admin.system-management.site-settings.show', compact('siteSetting'));
    }

    /**
     * Show the form for editing a site setting.
     */
    public function edit(SiteSetting $siteSetting)
    {
        return view('admin.system-management.site-settings.edit', compact('siteSetting'));
    }

    /**
     * Update the specified site setting.
     */
    public function update(Request $request, SiteSetting $siteSetting)
    {
        // Special validation for avatar gallery
        if ($siteSetting->key === 'available_avatars') {
            $validated = $request->validate([
                'key' => 'required|string|max:255|unique:site_settings,key,'.$siteSetting->id,
                'group' => 'required|string|max:255|in:general,contact,social,analytics,seo,footer,global,tools,other,email',
            ]);
        } elseif (in_array($siteSetting->key, ['site_logo', 'site_icon'], true)) {
            $fileRules = $siteSetting->key === 'site_logo'
                ? ['nullable', 'file', 'max:2048', 'mimes:jpeg,jpg,png,gif,webp,svg']
                : ['nullable', 'file', 'max:512', 'mimes:jpeg,jpg,png,gif,webp,svg,ico'];
            $fileField = $siteSetting->key === 'site_logo' ? 'site_logo_file' : 'site_icon_file';
            $validated = $request->validate([
                'key' => 'required|string|max:255|unique:site_settings,key,'.$siteSetting->id,
                'group' => 'required|string|max:255|in:general,contact,social,analytics,seo,footer,global,tools,other,email',
                $fileField => $fileRules,
            ]);
        } else {
            $validated = $request->validate([
                'key' => 'required|string|max:255|unique:site_settings,key,'.$siteSetting->id,
                'value' => 'required',
                'group' => 'required|string|max:255|in:general,contact,social,analytics,seo,footer,global,tools,other,email',
            ]);
        }

        // Special handling for avatar gallery uploads
        if ($siteSetting->key === 'available_avatars') {
            $avatarResult = $this->handleAvatarUploads($request, $siteSetting->value ?? []);
            $validated['value'] = $avatarResult;

        } elseif (in_array($siteSetting->key, ['site_logo', 'site_icon'], true)) {
            $validated['value'] = $this->handleSiteBrandingUpload($request, $siteSetting->key, $siteSetting->value);

        } else {
            $value = $request->input('value');

            // Check if the value is a JSON string
            if (is_string($value)) {
                $decoded = json_decode($value, true);

                // If JSON decode was successful and result is an array or object, use decoded value
                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                    $validated['value'] = $decoded;
                } else {
                    // If it's not valid JSON, treat as regular string
                    // But check length to prevent extremely long strings
                    if (strlen($value) > 10000) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['value' => 'Value is too long. Maximum 10000 characters allowed.']);
                    }
                    $validated['value'] = $value;
                }
            } else {
                // If value is already an array/object, use it directly
                $validated['value'] = $value;
            }
        }

        $oldData = [
            'key' => $siteSetting->key,
            'group' => $siteSetting->group,
            'value' => $this->formatValueForAudit($siteSetting->value),
        ];
        $siteSetting->update($validated);
        $newData = [
            'key' => $siteSetting->key,
            'group' => $siteSetting->group,
            'value' => $this->formatValueForAudit($siteSetting->value),
        ];
        AuditLogService::logSettingsUpdate($siteSetting->key, $oldData, $newData);
        if ($siteSetting->key === 'available_avatars' && $request->hasFile('avatar_files')) {
            $avatarCount = count($request->file('avatar_files'));
            AuditLogService::logUpload('Avatar gallery', ['context' => 'Avatar gallery', 'avatars_added' => $avatarCount]);
        }
        if (in_array($siteSetting->key, ['site_logo', 'site_icon'], true)) {
            $fileField = $siteSetting->key === 'site_logo' ? 'site_logo_file' : 'site_icon_file';
            if ($request->hasFile($fileField)) {
                AuditLogService::logUpload('Site branding', ['context' => $siteSetting->key]);
            }
        }

        return redirect()->route('system-management.site-settings.index')->with('success', 'Site setting updated successfully');
    }

    private function formatValueForAudit($value): mixed
    {
        if (is_string($value)) {
            return Str::limit($value, 500);
        }
        if (is_array($value)) {
            return Str::limit(json_encode($value), 500);
        }

        return $value;
    }

    /**
     * Handle avatar uploads for the gallery
     */
    private function handleAvatarUploads(Request $request, array $currentAvatars = []): array
    {
        $originalAvatars = $currentAvatars;
        $avatars = $currentAvatars;

        if ($request->hasFile('avatar_files')) {
            $files = $request->file('avatar_files');

            if (! is_array($files)) {
                $files = [$files];
            }

            $uploadPath = public_path('images/avatars/gallery');
            if (! file_exists($uploadPath)) {
                if (! mkdir($uploadPath, 0777, true)) {
                    throw new \Exception('Failed to create upload directory: '.$uploadPath);
                }
            }

            if (! is_writable($uploadPath)) {
                if (! chmod($uploadPath, 0777)) {
                    throw new \Exception('Failed to set permissions on upload directory: '.$uploadPath);
                }
            }

            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $filename = time().uniqid().'.'.$extension;
                    $path = 'images/avatars/gallery/'.$filename;

                    try {
                        $file->move($uploadPath, $filename);

                        $fullFilePath = $uploadPath.'/'.$filename;

                        if (file_exists($fullFilePath)) {
                            if (! in_array($path, $avatars)) {
                                $avatars[] = $path;
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to upload avatar file: '.$e->getMessage(), [
                            'filename' => $filename,
                            'original_name' => $file->getClientOriginalName(),
                            'upload_path' => $uploadPath,
                            'path_writable' => is_writable($uploadPath),
                            'path_exists' => file_exists($uploadPath),
                        ]);
                        throw $e;
                    }
                }
            }
        }

        $keepAvatars = $request->input('keep_avatars', []);
        if (! is_array($keepAvatars)) {
            $keepAvatars = [$keepAvatars];
        }

        $removedAvatars = array_diff($originalAvatars, $keepAvatars);
        foreach ($removedAvatars as $removedAvatar) {
            if (str_contains($removedAvatar, 'images/avatars/gallery/')) {
                $fullPath = public_path($removedAvatar);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $avatars = array_filter($avatars, function ($avatar) use ($keepAvatars, $originalAvatars) {
            if (in_array($avatar, $originalAvatars)) {
                return in_array($avatar, $keepAvatars);
            }

            return true;
        });

        $result = array_values($avatars);

        return $result;
    }

    /**
     * Handle single-file upload for site_logo / site_icon (paths stored as string).
     */
    private function handleSiteBrandingUpload(Request $request, string $key, mixed $currentValue): string
    {
        $default = $key === 'site_icon' ? SiteSetting::DEFAULT_SITE_ICON : SiteSetting::DEFAULT_SITE_LOGO;
        $fileField = $key === 'site_icon' ? 'site_icon_file' : 'site_logo_file';
        $removeField = $key === 'site_icon' ? 'remove_site_icon' : 'remove_site_logo';

        $current = is_string($currentValue) && $currentValue !== '' ? $currentValue : $default;

        if ($request->hasFile($fileField) && $request->file($fileField)->isValid()) {
            $uploadPath = public_path(trim(SiteSetting::BRANDING_UPLOAD_PREFIX, '/'));
            if (! file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file = $request->file($fileField);
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');
            $prefix = $key === 'site_icon' ? 'icon' : 'logo';
            $filename = $prefix.'-'.time().'-'.uniqid('', true).'.'.$extension;
            $file->move($uploadPath, $filename);

            $newPath = SiteSetting::BRANDING_UPLOAD_PREFIX.$filename;

            if (SiteSetting::isManagedBrandingPath($current) && $current !== $newPath) {
                $oldFull = public_path($current);
                if (file_exists($oldFull)) {
                    unlink($oldFull);
                }
            }

            return $newPath;
        }

        if ($request->boolean($removeField)) {
            if (SiteSetting::isManagedBrandingPath($current)) {
                $full = public_path($current);
                if (file_exists($full)) {
                    unlink($full);
                }
            }

            return $default;
        }

        return $current;
    }

    /**
     * Remove the specified site setting.
     */
    public function destroy(SiteSetting $siteSetting)
    {
        if (in_array($siteSetting->key, ['site_logo', 'site_icon'], true) && is_string($siteSetting->value)) {
            if (SiteSetting::isManagedBrandingPath($siteSetting->value)) {
                $full = public_path($siteSetting->value);
                if (file_exists($full)) {
                    unlink($full);
                }
            }
        }

        if ($siteSetting->key === 'available_avatars' && is_array($siteSetting->value)) {
            foreach ($siteSetting->value as $avatarPath) {
                if (str_contains($avatarPath, 'images/avatars/gallery/')) {
                    $fullPath = public_path($avatarPath);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
        }

        $key = $siteSetting->key;
        $siteSetting->delete();

        AuditLogService::logDelete(AuditLog::MODULE_SITE_SETTING, $key);

        return redirect()->route('system-management.site-settings.index')->with('success', 'Site setting deleted successfully');
    }
}
