<h3 class="edit-section-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
    </svg>
    Home Page Sections
</h3>
@php
    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];
    $n = function ($key) use ($locale) {
        return "homepage_sections[{$locale}][{$key}]";
    };
    $v = function ($key, $default = '') use ($d) {
        return $d[$key] ?? $default;
    };
@endphp

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">1</span> Hero Section ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Hero Headline</label>
            <input type="text" name="{{ $n('hero_headline') }}"
                class="form-input @error('homepage_sections.*.hero_headline') error @enderror"
                value="{{ old($n('hero_headline'), $v('hero_headline')) }}">
            @error('homepage_sections.*.hero_headline')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group full-width" data-shared-image="hero_image">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "homepage_sections[{$locale}][hero_image_file]",
                'pathName' => "homepage_sections[{$locale}][hero_image]",
                'label' => 'Hero Image',
                'pathValue' => $v('hero_image', ''),
                'currentImageUrl' => $v('hero_image'),
                'pathOnly' => false,
                'uploadPath' => 'images/homepage',
                'sharedImageKey' => 'hero_image',
            ])
        </div>
    </div>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Hero Description</label>
            <textarea name="{{ $n('hero_description') }}" rows="2"
                class="form-input form-textarea @error('homepage_sections.*.hero_description') error @enderror">{{ old($n('hero_description'), $v('hero_description')) }}</textarea>
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">2</span> Organization Overview ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <div class="form-group">
                <label class="form-label">Organization Title</label>
                <input type="text" name="{{ $n('company_title') }}" class="form-input"
                    value="{{ old($n('company_title'), $v('company_title', 'Our Mission')) }}">
            </div>
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "homepage_sections[{$locale}][company_logo_file]",
                'pathName' => "homepage_sections[{$locale}][company_logo]",
                'label' => 'Organization Logo',
                'pathValue' => $v('company_logo', ''),
                'currentImageUrl' => $v('company_logo'),
                'pathOnly' => false,
                'uploadPath' => 'images/homepage',
                'sharedImageKey' => 'company_logo',
            ])
        </div>
        <div class="form-group full-width">
            <label class="form-label">Organization Description</label>
            <textarea name="{{ $n('company_description') }}" rows="3" class="form-input form-textarea">{{ old($n('company_description'), $v('company_description')) }}</textarea>
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">3</span> Impact Pillars ({{ $localeName }})</h4>
    <div class="form-grid form-grid-3">
        <div class="form-group">
            <label class="form-label">Pillar 1 Title</label>
            <input type="text" name="{{ $n('value_prop_1_title') }}" class="form-input"
                value="{{ old($n('value_prop_1_title'), $v('value_prop_1_title')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Pillar 2 Title</label>
            <input type="text" name="{{ $n('value_prop_2_title') }}" class="form-input"
                value="{{ old($n('value_prop_2_title'), $v('value_prop_2_title')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Pillar 3 Title</label>
            <input type="text" name="{{ $n('value_prop_3_title') }}" class="form-input"
                value="{{ old($n('value_prop_3_title'), $v('value_prop_3_title')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Pillar 1 Description</label>
            <textarea name="{{ $n('value_prop_1_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('value_prop_1_desc'), $v('value_prop_1_desc')) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Pillar 2 Description</label>
            <textarea name="{{ $n('value_prop_2_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('value_prop_2_desc'), $v('value_prop_2_desc')) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Pillar 3 Description</label>
            <textarea name="{{ $n('value_prop_3_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('value_prop_3_desc'), $v('value_prop_3_desc')) }}</textarea>
        </div>
    </div>
    @include('admin.system-management.pages.partials.homepage-image-upload', [
        'name' => "homepage_sections[{$locale}][capabilities_image_file]",
        'pathName' => "homepage_sections[{$locale}][capabilities_image]",
        'label' => 'Impact Image',
        'pathValue' => $v('capabilities_image', ''),
        'currentImageUrl' => $v('capabilities_image'),
        'pathOnly' => false,
        'uploadPath' => 'images/homepage',
        'sharedImageKey' => 'capabilities_image',
    ])
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">4</span> Call to Action ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">CTA Title</label>
            <input type="text" name="{{ $n('marketing_title') }}" class="form-input"
                value="{{ old($n('marketing_title'), $v('marketing_title', 'Get involved')) }}">
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "homepage_sections[{$locale}][marketing_image_file]",
                'pathName' => "homepage_sections[{$locale}][marketing_image]",
                'label' => 'CTA Image',
                'pathValue' => $v('marketing_image', ''),
                'currentImageUrl' => $v('marketing_image'),
                'pathOnly' => false,
                'uploadPath' => 'images/homepage',
                'sharedImageKey' => 'marketing_image',
            ])
        </div>
    </div>
    <div class="form-group full-width">
        <label class="form-label">CTA Description</label>
        <textarea name="{{ $n('marketing_description') }}" rows="3" class="form-input form-textarea">{{ old($n('marketing_description'), $v('marketing_description')) }}</textarea>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">5</span> Trust Section ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Section Label</label>
            <input type="text" name="{{ $n('trust_subtitle') }}" class="form-input"
                value="{{ old($n('trust_subtitle'), $v('trust_subtitle', 'Built on trust')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Section Title</label>
            <input type="text" name="{{ $n('trust_title') }}" class="form-input"
                value="{{ old($n('trust_title'), $v('trust_title', 'Support that feels local, practical, and accountable.')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Section Description</label>
            <textarea name="{{ $n('trust_description') }}" rows="3" class="form-input form-textarea">{{ old($n('trust_description'), $v('trust_description', 'NGO work is strongest when it stays close to the people it serves. We listen first, respond with simple action, and keep donors and partners informed along the way.')) }}</textarea>
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "homepage_sections[{$locale}][trust_image_file]",
                'pathName' => "homepage_sections[{$locale}][trust_image]",
                'label' => 'Trust Image',
                'pathValue' => $v('trust_image', ''),
                'currentImageUrl' => $v('trust_image'),
                'pathOnly' => false,
                'uploadPath' => 'images/homepage',
                'sharedImageKey' => 'trust_image',
            ])
        </div>
    </div>

    <div class="form-grid form-grid-3">
        <div class="form-group">
            <label class="form-label">Step 1 Title</label>
            <input type="text" name="{{ $n('trust_step_1_title') }}" class="form-input"
                value="{{ old($n('trust_step_1_title'), $v('trust_step_1_title', 'Listen to the community')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Step 2 Title</label>
            <input type="text" name="{{ $n('trust_step_2_title') }}" class="form-input"
                value="{{ old($n('trust_step_2_title'), $v('trust_step_2_title', 'Act with purpose')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Step 3 Title</label>
            <input type="text" name="{{ $n('trust_step_3_title') }}" class="form-input"
                value="{{ old($n('trust_step_3_title'), $v('trust_step_3_title', 'Show the outcome')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Step 1 Description</label>
            <textarea name="{{ $n('trust_step_1_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('trust_step_1_desc'), $v('trust_step_1_desc', 'We work with local families, schools, and leaders to understand what matters most.')) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Step 2 Description</label>
            <textarea name="{{ $n('trust_step_2_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('trust_step_2_desc'), $v('trust_step_2_desc', 'Every program is designed to be useful, visible, and easy to support.')) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Step 3 Description</label>
            <textarea name="{{ $n('trust_step_3_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('trust_step_3_desc'), $v('trust_step_3_desc', 'We keep the story transparent so people can see the impact of their help.')) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Quote Label</label>
            <input type="text" name="{{ $n('trust_quote_label') }}" class="form-input"
                value="{{ old($n('trust_quote_label'), $v('trust_quote_label', 'Why it matters')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Quote Text</label>
            <textarea name="{{ $n('trust_quote_text') }}" rows="2" class="form-input form-textarea">{{ old($n('trust_quote_text'), $v('trust_quote_text', 'Small, clear actions build trust, and trust makes long-term community support possible.')) }}</textarea>
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">6</span> Impact Story ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Story Title</label>
            <input type="text" name="{{ $n('mobile_title') }}" class="form-input"
                value="{{ old($n('mobile_title'), $v('mobile_title', 'Impact in action')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Feature Label</label>
            <input type="text" name="{{ $n('impact_feature_label') }}" class="form-input"
                value="{{ old($n('impact_feature_label'), $v('impact_feature_label', 'What we focus on')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Feature 1 Title</label>
            <input type="text" name="{{ $n('impact_feature_1_title') }}" class="form-input"
                value="{{ old($n('impact_feature_1_title'), $v('impact_feature_1_title', 'Community updates')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Feature 2 Title</label>
            <input type="text" name="{{ $n('impact_feature_2_title') }}" class="form-input"
                value="{{ old($n('impact_feature_2_title'), $v('impact_feature_2_title', 'Transparent reporting')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Feature 3 Title</label>
            <input type="text" name="{{ $n('impact_feature_3_title') }}" class="form-input"
                value="{{ old($n('impact_feature_3_title'), $v('impact_feature_3_title', 'Direct response')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Feature 1 Description</label>
            <textarea name="{{ $n('impact_feature_1_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('impact_feature_1_desc'), $v('impact_feature_1_desc', 'Short, clear updates that show what is happening on the ground.')) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Feature 2 Description</label>
            <textarea name="{{ $n('impact_feature_2_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('impact_feature_2_desc'), $v('impact_feature_2_desc', 'Simple reporting that helps supporters understand the results.')) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Feature 3 Description</label>
            <textarea name="{{ $n('impact_feature_3_desc') }}" rows="2" class="form-input form-textarea">{{ old($n('impact_feature_3_desc'), $v('impact_feature_3_desc', 'Fast action when families need practical help the most.')) }}</textarea>
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "homepage_sections[{$locale}][mobile_image_file]",
                'pathName' => "homepage_sections[{$locale}][mobile_image]",
                'label' => 'Story Image',
                'pathValue' => $v('mobile_image', ''),
                'currentImageUrl' => $v('mobile_image'),
                'pathOnly' => false,
                'uploadPath' => 'images/homepage',
                'sharedImageKey' => 'mobile_image',
            ])
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "homepage_sections[{$locale}][mobile_bg_file]",
                'pathName' => "homepage_sections[{$locale}][mobile_bg]",
                'label' => 'Story Background',
                'pathValue' => $v('mobile_bg', ''),
                'currentImageUrl' => $v('mobile_bg'),
                'pathOnly' => false,
                'uploadPath' => 'images/homepage',
                'sharedImageKey' => 'mobile_bg',
            ])
        </div>
    </div>
</div>
