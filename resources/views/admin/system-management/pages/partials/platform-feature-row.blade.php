{{--
    Platform "Our Features" repeater row.
    $locale, $index (int), $title, $color, $icon, $platformFeatureIcons
--}}
@php
    $platformFeatureIcons = $platformFeatureIcons ?? config('platform_feature_icons.icons', []);
    $color = in_array($color ?? 'blue', ['blue', 'purple', 'green', 'red'], true) ? ($color ?? 'blue') : 'blue';
    $iconKeys = array_keys($platformFeatureIcons);
    $icon = in_array($icon ?? '', $iconKeys, true) ? ($icon ?? $iconKeys[0] ?? 'icon_1') : ($iconKeys[0] ?? 'icon_1');
    $indexStr = (string) ($index ?? 0);
    $featureNumLabel = $indexStr === '__INDEX__' ? '#' : ((int) $index + 1);
@endphp
<div class="platform-feature-row" data-platform-feature-row>
    <div class="platform-feature-row-header">
        <span class="form-label platform-feature-row-label">Feature <span
                class="platform-feature-num">{{ $featureNumLabel }}</span></span>
        <button type="button" class="btn btn-outline btn-sm" data-remove-platform-feature
            title="Remove this feature">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" width="16" height="16" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Remove
        </button>
    </div>
    <div class="form-grid form-grid-3">
        <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="platform_sections[{{ $locale }}][features][{{ $indexStr }}][title]"
                class="form-input" value="{{ $title ?? '' }}" placeholder="Feature title">
        </div>
        <div class="form-group">
            <label class="form-label">Color</label>
            <select name="platform_sections[{{ $locale }}][features][{{ $indexStr }}][color]" class="form-input">
                <option value="blue" {{ $color === 'blue' ? 'selected' : '' }}>Blue</option>
                <option value="purple" {{ $color === 'purple' ? 'selected' : '' }}>Purple</option>
                <option value="green" {{ $color === 'green' ? 'selected' : '' }}>Green</option>
                <option value="red" {{ $color === 'red' ? 'selected' : '' }}>Red</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Icon</label>
            <select name="platform_sections[{{ $locale }}][features][{{ $indexStr }}][icon]" class="form-input">
                @foreach ($platformFeatureIcons as $key => $option)
                    <option value="{{ $key }}" {{ $icon === $key ? 'selected' : '' }}>
                        {{ $option['label'] ?? $key }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
