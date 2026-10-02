{{--
    Repeatable list. Expects: $code, $key, $field, $isBase, $baseItems, $translatedItems.
    English: add / remove / reorder items. Other languages: translate each English item.
--}}
@php
    $itemLabel = $field['item_label'] ?? 'Item';
    $imageKey = collect($field['fields'])->search(fn ($f) => $f['type'] === 'image');
    $emptyItem = collect($field['fields'])->map(fn ($f) => $f['default'] ?? '')->all();
@endphp
<div class="sc-list" data-sc-list>
    <div class="sc-list-head">
        <span class="form-label">{{ $field['label'] }}</span>
        <span class="sc-list-count" data-sc-count>{{ count($baseItems) }}</span>
    </div>

    @if (!$isBase && empty($baseItems))
        <p class="sc-note">There are no English items yet.</p>
    @endif

    <div class="sc-items" data-sc-items>
        @foreach ($baseItems as $index => $item)
            @include('admin.system-management.pages.partials.schema-list-item', [
                'index' => $index,
                'values' => $isBase ? $item : ($translatedItems[$index] ?? []),
                'placeholders' => $isBase ? [] : $item,
            ])
        @endforeach
    </div>

    @if ($isBase)
        <template data-sc-template>
            @include('admin.system-management.pages.partials.schema-list-item', [
                'index' => '__INDEX__',
                'values' => $emptyItem,
                'placeholders' => [],
                'open' => true,
            ])
        </template>
        <div class="sc-list-actions">
            <button type="button" class="btn btn-outline sc-btn-sm" data-sc-add>+ Add {{ strtolower($itemLabel) }}</button>
            @if ($imageKey !== false)
                <label class="btn btn-outline sc-btn-sm sc-bulk">
                    + Add several images at once
                    <input type="file" accept="image/*" multiple data-sc-bulk="{{ $imageKey }}" hidden>
                </label>
            @endif
        </div>
    @elseif (!empty($baseItems))
        <small class="form-hint">Items are added, removed and reordered in the English tab. If you reorder English items, check these translations again.</small>
    @endif
</div>
