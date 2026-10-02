@extends('admin.layouts.app')

@php
    use App\Models\FormSubmission;

    $singular = FormSubmission::typeLabel($type, false);
    $rows = [
        'Name' => $submission->name,
        'Email' => $submission->email,
        'Phone' => $submission->phone,
        FormSubmission::TYPES[$type][2] => $submission->subject,
    ];
    if ($type === 'donate') {
        $rows['Amount'] = '$' . number_format((float) $submission->amount, 2);
    }
    foreach ((array) $submission->details as $key => $value) {
        $rows[\Illuminate\Support\Str::headline($key)] = is_scalar($value) ? (string) $value : json_encode($value);
    }
    $rows['Language'] = strtoupper((string) $submission->locale);
    $rows['Received'] = $submission->created_at?->format('M d, Y H:i');
    $rows['Email sent to team'] = $submission->email_sent ? 'Yes' : 'No (check mail settings)';
@endphp

@section('title', $singular)

@section('content')
    @include('admin.submissions.partials.styles')
    @include('admin.submissions.partials.alerts')

    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ $singular }}</h2>
            <p>{{ $submission->name }} · <span class="sub-badge sub-badge-{{ $submission->status }}">{{ $submission->statusLabel() }}</span></p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.submissions.index', $type) }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="sub-detail">
        <div class="form-card">
            <div class="sub-fields">
                @foreach ($rows as $label => $value)
                    @continue($value === null || $value === '')
                    <div><span>{{ $label }}</span><strong>{{ $value }}</strong></div>
                @endforeach
            </div>

            @if ($submission->message)
                <h3 class="sub-heading">Message</h3>
                <div class="sub-message">{!! nl2br(e($submission->message)) !!}</div>
            @endif

            @if ($submission->attachment_path)
                <a href="{{ route('admin.submissions.attachment', [$type, $submission]) }}" class="btn btn-info" style="margin-top:16px">
                    📎 Download {{ $submission->attachment_name ?: 'attachment' }}
                </a>
            @endif

            <div class="sub-actions">
                <a href="mailto:{{ $submission->email }}?subject={{ rawurlencode('Re: ' . ($submission->subject ?: $singular)) }}" class="btn btn-secondary">Reply by email</a>
                @if ($submission->phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $submission->phone) }}" class="btn btn-secondary">Call</a>
                @endif
            </div>
        </div>

        <div class="form-card">
            <h3 class="sub-heading" style="margin-top:0">Follow-up</h3>
            @can('submissions.edit')
                <form action="{{ route('admin.submissions.update', [$type, $submission]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-input">
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($submission->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="admin_note">Internal note</label>
                        <textarea name="admin_note" id="admin_note" rows="5" class="form-input form-textarea"
                            placeholder="Only visible to the team">{{ old('admin_note', $submission->admin_note) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-info">Save</button>
                </form>
            @else
                <p>Status: {{ $submission->statusLabel() }}</p>
                @if ($submission->admin_note)<p>{{ $submission->admin_note }}</p>@endif
            @endcan

            @can('submissions.delete')
                <form action="{{ route('admin.submissions.destroy', [$type, $submission]) }}" method="POST" style="margin-top:24px"
                    data-confirm-delete="Delete this {{ strtolower($singular) }}? This cannot be undone." data-delete-title="Delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            @endcan
        </div>
    </div>
@endsection
