{{-- One list item. Expects: $code, $key, $field, $isBase, $index, $values, $placeholders, optional $open --}}
@php
    $summaryKey = collect(['title', 'name', 'label', 'year', 'text', 'amount', 'number'])
        ->first(fn ($k) => isset($field['fields'][$k]));
    $summary = $summaryKey ? (string) (($values[$summaryKey] ?? '') ?: ($placeholders[$summaryKey] ?? '')) : '';
    $thumb = collect($field['fields'])->search(fn ($f) => $f['type'] === 'image');
    $thumbUrl = $thumb !== false ? \App\Support\PageContent::url((string) (($isBase ? $values : $placeholders)[$thumb] ?? '')) : '';
@endphp
<details class="sc-item" data-sc-item @if (!empty($open)) open @endif>
    <summary>
        <span class="sc-item-title">
            {{ $field['item_label'] ?? 'Item' }} <span data-sc-num>{{ is_int($index) ? $index + 1 : '' }}</span>
            @if ($thumbUrl !== '')
                <img src="{{ $thumbUrl }}" alt="" class="sc-item-thumb" loading="lazy">
            @endif
            <span class="sc-item-summary">{{ \Illuminate\Support\Str::limit(strip_tags($summary), 60) }}</span>
        </span>
        @if ($isBase)
            <span class="sc-item-tools">
                <button type="button" class="sc-icon-btn" data-sc-up title="Move up">↑</button>
                <button type="button" class="sc-icon-btn" data-sc-down title="Move down">↓</button>
                <button type="button" class="sc-icon-btn sc-danger" data-sc-remove title="Remove">✕</button>
            </span>
        @endif
    </summary>
    <div class="sc-item-body">
        @foreach ($field['fields'] as $subKey => $sub)
            @continue(!$isBase && !\App\Support\ContentSchema::isTranslatable($sub))
            @include('admin.system-management.pages.partials.schema-field', [
                'field' => $sub,
                'name' => "blocks[{$code}][{$key}][{$index}][{$subKey}]",
                'fileName' => "block_files[{$code}][{$key}][{$index}][{$subKey}]",
                'value' => $values[$subKey] ?? '',
                'placeholder' => is_scalar($placeholders[$subKey] ?? null) ? (string) $placeholders[$subKey] : '',
            ])
        @endforeach
    </div>
</details>
