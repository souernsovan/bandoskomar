@extends('admin.layouts.app')

@section('title', 'Audit Log Details')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Audit Log Details</h2>
            <p>View full details of this audit log entry</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.audit-logs.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <div class="banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div class="detail-header-info">
                <h3>
                    @if ($batchLogs ?? null)
                        {{ \App\Models\AuditLog::getBatchGroupLabel($batchLogs->all()) }}
                    @else
                        {{ $auditLog->getActionLabel() }} — {{ $auditLog->getModuleLabel() }}
                    @endif
                </h3>
                <p>{{ $auditLog->user ? $auditLog->user->name . ' - ' . $auditLog->user->email : '—' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Date/Time</label>
                        <span class="datetime"
                            data-iso="{{ $auditLog->created_at->toIso8601String() }}">{{ $auditLog->created_at->format('M d Y, h:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Created By</label>
                        <span class="logs-user-name">{{ $auditLog->user ? $auditLog->user->name : '—' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Action</label>
                        <span class="badge badge-action-{{ $auditLog->action }}">
                            {{ $auditLog->getActionLabel() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- What Changed - show first for quick understanding -->
            @if ($batchLogs ?? null)
                @foreach ($batchLogs as $batchLog)
                    @php
                        $allKeys = array_unique(
                            array_merge(array_keys($batchLog->old_value ?? []), array_keys($batchLog->new_value ?? [])),
                        );
                        $changeKeys = array_filter($allKeys, fn($k) => $k !== 'object_changed');
                    @endphp
                    @if (!empty($changeKeys))
                        <div class="detail-section audit-change-block">
                            @php
                                $sectionTitle = $batchLog->object_changed ?? 'Changes';
                                $sectionTitle = preg_replace('/\s*\([^)]+\)\s*$/', '', $sectionTitle);
                            @endphp
                            <h4 class="detail-section-title audit-block-title">{{ $sectionTitle }}</h4>
                            <div class="changes-comparison">
                                @foreach ($changeKeys as $key)
                                    <div class="comparison-row">
                                        <div class="comparison-label">
                                            {{ \App\Helpers\AuditLogFormatter::getHumanLabel($key) }}</div>
                                        <div class="comparison-values">
                                            <div class="comparison-cell comparison-cell-old">
                                                <span class="comparison-badge comparison-badge-old">Before</span>
                                                <div class="comparison-content">
                                                    @php
                                                        $oldVal = ($batchLog->old_value ?? [])[$key] ?? null;
                                                        $oldFormatted = \App\Helpers\AuditLogFormatter::formatValue(
                                                            $key,
                                                            $oldVal,
                                                        );
                                                    @endphp
                                                    @if ($oldFormatted['formatted'] ?? false)
                                                        {!! $oldFormatted['html'] !!}
                                                    @elseif($oldVal === null)
                                                        <span class="comparison-null">—</span>
                                                    @elseif(is_array($oldVal) || is_object($oldVal))
                                                        <pre class="comparison-json">{{ json_encode($oldVal, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $oldVal }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="comparison-arrow">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" width="18" height="18">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"></path>
                                                </svg>
                                            </div>
                                            <div class="comparison-cell comparison-cell-new">
                                                <span class="comparison-badge comparison-badge-new">After</span>
                                                <div class="comparison-content">
                                                    @php
                                                        $newVal = ($batchLog->new_value ?? [])[$key] ?? null;
                                                        $newFormatted = \App\Helpers\AuditLogFormatter::formatValue(
                                                            $key,
                                                            $newVal,
                                                        );
                                                    @endphp
                                                    @if ($newFormatted['formatted'] ?? false)
                                                        {!! $newFormatted['html'] !!}
                                                    @elseif($newVal === null)
                                                        <span class="comparison-null">—</span>
                                                    @elseif(is_array($newVal) || is_object($newVal))
                                                        <pre class="comparison-json">{{ json_encode($newVal, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $newVal }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                @php
                    $allKeys = array_unique(
                        array_merge(array_keys($auditLog->old_value ?? []), array_keys($auditLog->new_value ?? [])),
                    );
                    $changeKeys = array_filter($allKeys, fn($k) => $k !== 'object_changed');
                @endphp
                @if (!empty($changeKeys))
                    <div class="detail-section audit-change-block">
                        <h4 class="detail-section-title audit-block-title">What changed</h4>
                        <div class="changes-comparison">
                            @foreach ($changeKeys as $key)
                                <div class="comparison-row">
                                    <div class="comparison-label">{{ \App\Helpers\AuditLogFormatter::getHumanLabel($key) }}
                                    </div>
                                    <div class="comparison-values">
                                        <div class="comparison-cell comparison-cell-old">
                                            <span class="comparison-badge comparison-badge-old">Before</span>
                                            <div class="comparison-content">
                                                @php
                                                    $oldVal = ($auditLog->old_value ?? [])[$key] ?? null;
                                                    $oldFormatted = \App\Helpers\AuditLogFormatter::formatValue(
                                                        $key,
                                                        $oldVal,
                                                    );
                                                @endphp
                                                @if ($oldFormatted['formatted'] ?? false)
                                                    {!! $oldFormatted['html'] !!}
                                                @elseif($oldVal === null)
                                                    <span class="comparison-null">—</span>
                                                @elseif(is_numeric($oldVal))
                                                    <span class="comparison-number">{{ number_format($oldVal) }}</span>
                                                @elseif(is_array($oldVal) || is_object($oldVal))
                                                    <pre class="comparison-json">{{ json_encode($oldVal, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $oldVal }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="comparison-arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" width="18" height="18">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"></path>
                                            </svg>
                                        </div>
                                        <div class="comparison-cell comparison-cell-new">
                                            <span class="comparison-badge comparison-badge-new">After</span>
                                            <div class="comparison-content">
                                                @php
                                                    $newVal = ($auditLog->new_value ?? [])[$key] ?? null;
                                                    $newFormatted = \App\Helpers\AuditLogFormatter::formatValue(
                                                        $key,
                                                        $newVal,
                                                    );
                                                @endphp
                                                @if ($newFormatted['formatted'] ?? false)
                                                    {!! $newFormatted['html'] !!}
                                                @elseif($newVal === null)
                                                    <span class="comparison-null">—</span>
                                                @elseif(is_numeric($newVal))
                                                    <span class="comparison-number">{{ number_format($newVal) }}</span>
                                                @elseif(is_array($newVal) || is_object($newVal))
                                                    <pre class="comparison-json">{{ json_encode($newVal, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $newVal }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- Device & Browser -->
            <div class="detail-section">
                <h4 class="detail-section-title">Device &amp; browser</h4>
                <div class="device-info-card">
                    <div class="detail-grid form-grid-3">
                        <div class="detail-item">
                            <label>Device</label>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if ($deviceInfo ?? null)
                                    <span class="logs-device-browser-info">{{ $deviceInfo['device'] ?? 'Unknown' }}</span>
                                @else
                                    <span>—</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Browser</label>
                            <span class="logs-device-browser-info">{{ $deviceInfo['browser_full'] ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <label>IP address</label>
                            <span class="uuid-cell">{{ $auditLog->ip_address ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <span class="datetime"
                            data-iso="{{ $auditLog->created_at->toIso8601String() }}">{{ $auditLog->created_at->format('M d, Y \a\t h:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime"
                            data-iso="{{ $auditLog->updated_at->toIso8601String() }}">{{ $auditLog->updated_at->format('M d, Y \a\t h:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Audit Log ID</label>
                        <span class="uuid-cell">{{ $auditLog->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
