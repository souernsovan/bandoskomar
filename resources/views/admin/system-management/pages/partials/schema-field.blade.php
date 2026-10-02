{{-- One editable value. Expects: $field, $name, $fileName, $value, $placeholder --}}
@php
    $type = $field['type'];
    $value = is_array($value ?? null) ? '' : ($value ?? '');
    $placeholder = $placeholder ?? '';
@endphp
<div class="form-group sc-field sc-field-{{ $type }}">
    @if ($type !== 'checkbox')
        <label class="form-label">{{ $field['label'] }}</label>
    @endif

    @switch($type)
        @case('textarea')
        @case('rich')
            <textarea name="{{ $name }}" rows="{{ $type === 'rich' ? 5 : 3 }}" class="form-input form-textarea"
                placeholder="{{ $placeholder }}">{{ $value }}</textarea>
            @break

        @case('number')
            <input type="number" step="any" name="{{ $name }}" value="{{ $value }}" class="form-input">
            @break

        @case('checkbox')
            <input type="hidden" name="{{ $name }}" value="0">
            <label class="checkbox-label">
                <input type="checkbox" name="{{ $name }}" value="1" @checked(filter_var($value, FILTER_VALIDATE_BOOLEAN))>
                {{ $field['label'] }}
            </label>
            @break

        @case('select')
        @case('icon')
            <select name="{{ $name }}" class="form-input">
                @foreach (\App\Support\ContentSchema::options($field) as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('image')
        @case('file')
            @php $url = \App\Support\PageContent::url((string) $value); @endphp
            <div class="sc-media" data-sc-media data-kind="{{ $type }}">
                <div class="sc-media-preview" data-sc-media-preview>
                    @if ($url !== '' && $type === 'image')
                        <img src="{{ $url }}" alt="" loading="lazy">
                    @elseif ($url !== '')
                        <a href="{{ $url }}" target="_blank" rel="noopener">Open current file</a>
                    @else
                        <span>{{ $type === 'image' ? 'No image' : 'No file' }}</span>
                    @endif
                </div>
                <div class="sc-media-inputs">
                    <input type="text" name="{{ $name }}" value="{{ $value }}" class="form-input" data-sc-media-path
                        placeholder="{{ $type === 'image' ? 'Upload below, or paste an image link' : 'Upload below, or paste a PDF link' }}">
                    <div class="sc-media-actions">
                        <input type="file" name="{{ $fileName }}" data-sc-media-file
                            accept="{{ $type === 'image' ? 'image/*' : 'application/pdf' }}">
                        <button type="button" class="btn btn-secondary sc-btn-sm" data-sc-media-clear>Remove</button>
                    </div>
                </div>
            </div>
            @break

        @default
            <input type="text" name="{{ $name }}" value="{{ $value }}" class="form-input" placeholder="{{ $placeholder }}">
    @endswitch

    @if (!empty($field['hint']))
        <small class="form-hint">{{ $field['hint'] }}</small>
    @endif
</div>
