{{--
    Generic content editor generated from App\Support\ContentSchema.
    English edits everything; other languages only translate text.
--}}
@php
    use App\Support\ContentSchema;
    use App\Support\PageLocales;

    $baseLocale = PageLocales::default();
    $baseData = $pageContentByLocale[$baseLocale] ?? [];
    $baseValue = fn (string $key, array $field) => array_key_exists($key, $baseData) ? $baseData[$key] : ($field['default'] ?? '');
@endphp

@include('admin.system-management.pages.partials.schema-styles')

<div class="edit-page-section sc-editor" data-sc-editor>
    <h3 class="edit-section-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6ZM3.75 9h16.5M9 9v11.25" />
        </svg>
        Page content — {{ $schema['label'] }}
    </h3>

    @if ($errors->has('blocks'))
        <div class="sc-errors">
            @foreach ($errors->get('blocks') as $message)
                <p>{{ $message }}</p>
            @endforeach
        </div>
    @endif

    @foreach (PageLocales::labels() as $code => $info)
        @php
            $isBase = $code === $baseLocale;
            $localeData = $pageContentByLocale[$code] ?? [];
        @endphp
        <div class="lang-panel {{ $isBase ? 'active' : '' }}" id="lang-panel-content-{{ $code }}" role="tabpanel">
            @unless ($isBase)
                <p class="sc-note">
                    You are translating into <strong>{{ $info['name'] }}</strong>. Leave a box empty to show the English text.
                    Pictures, links, numbers and adding or removing list items are done in the English tab.
                </p>
            @endunless

            @foreach ($schema['sections'] as $section)
                @php
                    $visible = array_filter($section['fields'], fn ($f) => $isBase
                        || ($f['type'] === 'list' ? ContentSchema::listIsTranslatable($f) : ContentSchema::isTranslatable($f)));
                @endphp
                @continue(empty($visible))

                <details class="sc-section" @if ($loop->first) open @endif>
                    <summary>{{ $section['title'] }}</summary>
                    <div class="sc-section-body">
                        @foreach ($visible as $key => $field)
                            @if ($field['type'] === 'list')
                                @include('admin.system-management.pages.partials.schema-list', [
                                    'code' => $code,
                                    'key' => $key,
                                    'field' => $field,
                                    'isBase' => $isBase,
                                    'baseItems' => array_values(array_filter((array) $baseValue($key, $field), 'is_array')),
                                    'translatedItems' => array_values((array) ($localeData[$key] ?? [])),
                                ])
                            @else
                                @php
                                    $value = $isBase
                                        ? $baseValue($key, $field)
                                        : ($localeData[$key] ?? ($field['default_' . $code] ?? ''));
                                @endphp
                                @include('admin.system-management.pages.partials.schema-field', [
                                    'field' => $field,
                                    'name' => "blocks[{$code}][{$key}]",
                                    'fileName' => "block_files[{$code}][{$key}]",
                                    'value' => old("blocks.{$code}.{$key}", $value),
                                    'placeholder' => $isBase ? '' : (string) $baseValue($key, $field),
                                ])
                            @endif
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>
    @endforeach
</div>

@include('admin.system-management.pages.partials.schema-scripts')
