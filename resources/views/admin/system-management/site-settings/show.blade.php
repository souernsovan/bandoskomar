@extends('admin.layouts.app')

@section('title', 'View Site Setting')

@section('breadcrumb')
    <a href="{{ route('system-management.site-settings.index') }}" class="breadcrumb-item">Site Settings</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($siteSetting->key, 30) }}</span>
@endsection

@section('content')
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span class="alert-content">{{ session('success') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span class="alert-content">{{ session('error') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <div class="page-header">
        <div class="page-header-left">
            <h2>Site Setting Details</h2>
            <p>View site setting information</p>
        </div>

        <div class="page-header-right">
            @can('site_settings.edit')
            <a href="{{ route('system-management.site-settings.edit', $siteSetting) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                </svg>
                Edit
            </a>
            @endcan
            <a href="{{ route('system-management.site-settings.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="21" x2="4" y2="14" />
                    <line x1="4" y1="10" x2="4" y2="3" />
                    <line x1="12" y1="21" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12" y2="3" />
                    <line x1="20" y1="21" x2="20" y2="16" />
                    <line x1="20" y1="12" x2="20" y2="3" />
                    <line x1="2" y1="14" x2="6" y2="14" />
                    <line x1="10" y1="8" x2="14" y2="8" />
                    <line x1="18" y1="16" x2="22" y2="16" />
                  </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ Str::title(str_replace(['_', '-'], ' ', $siteSetting->key)) }}</h3>
                <p>{{ $siteSetting->getGroupDisplayName() }} Setting</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">
                    Basic Information
                </h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Setting Key</label>
                        <span class="badge text-blue bg-blue">{{ $siteSetting->key }}</span>
                        <small class="text-muted">Unique identifier for this setting</small>
                    </div>
                    <div class="detail-item">
                        <label>Configuration Group</label>
                        <span class="badge text-{{ $siteSetting->getGroupBadgeClass() }} bg-{{ $siteSetting->getGroupBadgeClass() }}">{{ $siteSetting->getGroupDisplayName() }}</span>
                        <small class="text-muted">Category for organization</small>
                    </div>
                    <div class="detail-item">
                        <label>Data Type</label>
                        @if(is_array($siteSetting->value))
                            <span class="badge text-blue bg-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                </svg>
                                Array ({{ count($siteSetting->value) }} items)
                            </span>
                        @elseif(is_bool($siteSetting->value))
                            <span class="badge text-{{ $siteSetting->value ? 'green' : 'red' }} bg-{{ $siteSetting->value ? 'green' : 'red' }}">
                                {{ $siteSetting->value ? 'Boolean (True)' : 'Boolean (False)' }}
                            </span>
                        @elseif(is_numeric($siteSetting->value))
                            <span class="badge text-yellow bg-yellow">Number ({{ is_int($siteSetting->value) ? 'Integer' : 'Float' }})</span>
                        @else
                            <span class="badge text-gray bg-gray">String/Text</span>
                        @endif
                        <small class="text-muted">Value data type</small>
                    </div>
                </div>
            </div>

            <!-- Value Section -->
            <div class="detail-section">
                <h4 class="detail-section-title">
                    Setting Value
                </h4>
                <div class="value-display-container">
                    @if($siteSetting->key === 'available_avatars')
                        <!-- Special display for available avatars -->
                        <div class="avatar-gallery-display">
                            @if(is_array($siteSetting->value) && !empty($siteSetting->value))
                                <div class="avatar-display-grid">
                                    @foreach($siteSetting->value as $avatar)
                                        <div class="avatar-display-item">
                                            <div class="avatar-image-container">
                                                <img src="{{ url($avatar) }}" alt="Avatar" class="avatar-display-image">
                                            </div>
                                            <div class="avatar-info">
                                                <div class="avatar-path">{{ basename($avatar) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 6h.008v.008H6V6Z" />
                                </svg>
                                <h5 class="text-muted">No Avatars Configured</h5>
                                <p class="text-muted">Upload new avatars above to get started.</p>
                            </div>
                            @endif
                        </div>
                    @elseif(in_array($siteSetting->key, ['site_logo', 'site_icon'], true))
                        @php
                            $brandingPath = is_string($siteSetting->value)
                                ? $siteSetting->value
                                : ($siteSetting->key === 'site_icon'
                                    ? \App\Models\SiteSetting::DEFAULT_SITE_ICON
                                    : \App\Models\SiteSetting::DEFAULT_SITE_LOGO);
                            $brandingMaxH = $siteSetting->key === 'site_icon' ? '64px' : '120px';
                        @endphp
                        <div class="site-branding-preview">
                            <div class="avatar-image-container" style="max-width: 320px; margin-bottom: 0.75rem;">
                                <img src="{{ asset($brandingPath) }}" alt="{{ $siteSetting->key }}"
                                    class="avatar-display-image"
                                    style="object-fit: contain; max-height: {{ $brandingMaxH }}; width: auto;">
                            </div>
                        </div>
                    @elseif(is_array($siteSetting->value))
                        <div class="json-value-section">
                            <div class="json-header">
                                <span class="json-label">JSON Configuration</span>
                                <button type="button" class="btn btn-sm btn-secondary copy-json-btn" data-json="{{ json_encode($siteSetting->value, JSON_PRETTY_PRINT) }}" onclick="copyToClipboard(event)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
                                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
                                      </svg>
                                    Copy JSON
                                </button>
                            </div>
                            <pre class="json-display" id="json-content">{{ json_encode($siteSetting->value, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @elseif(is_bool($siteSetting->value))
                        <div class="boolean-value-display">
                            <div class="boolean-indicator {{ $siteSetting->value ? 'boolean-true' : 'boolean-false' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="boolean-icon">
                                    @if($siteSetting->value)
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    @endif
                                </svg>
                                <span class="boolean-text">{{ $siteSetting->value ? 'TRUE' : 'FALSE' }}</span>
                            </div>
                            <div class="boolean-description">
                                <strong>{{ $siteSetting->value ? 'Enabled' : 'Disabled' }}</strong>
                                <span>{{ $siteSetting->value ? 'This setting is active and enabled.' : 'This setting is inactive and disabled.' }}</span>
                            </div>
                        </div>
                    @elseif(is_numeric($siteSetting->value))
                        <div class="numeric-value-display">
                            <div class="numeric-value">{{ number_format($siteSetting->value) }}</div>
                            <div class="numeric-type">{{ is_int($siteSetting->value) ? 'Integer' : 'Decimal' }} Value</div>
                        </div>
                    @else
                        <div class="">
                            <div class="">{{ $siteSetting->value ?? 'No value set' }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Usage Information (for specific keys) -->
            @if(in_array($siteSetting->key, ['site_name', 'site_logo', 'site_icon', 'contact_email', 'social_media_links', 'footer_links', 'seo_meta_title', 'available_avatars']))
            <div class="detail-section">
                <h4 class="detail-section-title">
                    Usage Information
                </h4>
                <div class="usage-info">
                    @switch($siteSetting->key)
                        @case('site_name')
                            <div class="usage-item">
                                <h5>Site Branding</h5>
                                <p>Used in page titles, headers, and branding elements throughout the site.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">Header Logo</span>
                                <span class="usage-tag">Page Titles</span>
                                <span class="usage-tag">Footer</span>
                                <span class="usage-tag">Meta Tags</span>
                            </div>
                            @break
                        @case('site_logo')
                            <div class="usage-item">
                                <h5>Site logo image</h5>
                                <p>Main logo shown in the admin sidebar, admin login, and public site header.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">Admin sidebar</span>
                                <span class="usage-tag">Login page</span>
                                <span class="usage-tag">Frontend header</span>
                            </div>
                            @break
                        @case('site_icon')
                            <div class="usage-item">
                                <h5>Favicon</h5>
                                <p>Small icon shown in browser tabs and bookmarks.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">Admin panel</span>
                                <span class="usage-tag">Login page</span>
                                <span class="usage-tag">Public site</span>
                            </div>
                            @break
                        @case('contact_email')
                            <div class="usage-item">
                                <h5>Support Communications</h5>
                                <p>Primary email address for user support, system notifications, and contact forms.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">Contact Forms</span>
                                <span class="usage-tag">Support Tickets</span>
                                <span class="usage-tag">Footer</span>
                                <span class="usage-tag">System Emails</span>
                            </div>
                            @break
                        @case('social_media_links')
                            <div class="usage-item">
                                <h5>Social Media Integration</h5>
                                <p>Social platform links displayed in footer and potentially other locations.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">Footer</span>
                                <span class="usage-tag">Social Widgets</span>
                                <span class="usage-tag">Share Buttons</span>
                            </div>
                            @break
                        @case('footer_links')
                            <div class="usage-item">
                                <h5>Site Navigation</h5>
                                <p>Footer navigation links organized by category for site structure.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">Footer Menu</span>
                                <span class="usage-tag">Sitemap</span>
                                <span class="usage-tag">SEO</span>
                            </div>
                            @break
                        @case('seo_meta_title')
                            <div class="usage-item">
                                <h5>Search Engine Optimization</h5>
                                <p>Default page title used by search engines and social media sharing.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">Meta Title</span>
                                <span class="usage-tag">Open Graph</span>
                                <span class="usage-tag">Twitter Cards</span>
                            </div>
                            @break
                        @case('available_avatars')
                            <div class="usage-item">
                                <h5>User Profile Customization</h5>
                                <p>Collection of avatar images that users can select from when setting up their profiles.</p>
                            </div>
                            <div class="usage-locations">
                                <span class="usage-tag">User Profiles</span>
                                <span class="usage-tag">Account Settings</span>
                                <span class="usage-tag">Registration</span>
                                <span class="usage-tag">Profile Edit</span>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>
            @endif

            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">
                    Metadata
                </h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <span class="datetime" data-iso="{{ $siteSetting->created_at->toIso8601String() }}">
                            {{ $siteSetting->created_at->format('F d, Y \a\t h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $siteSetting->updated_at->toIso8601String() }}">
                            {{ $siteSetting->updated_at->format('F d, Y \a\t h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Setting ID</label>
                        <span class="uuid-cell">{{ $siteSetting->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
