<h3 class="edit-section-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 18.75v3.75m-8.25-3.75h16.5A2.25 2.25 0 0 0 22.5 16.5v-9A2.25 2.25 0 0 0 20.25 5.25H3.75A2.25 2.25 0 0 0 1.5 7.5v9a2.25 2.25 0 0 0 2.25 2.25Zm4.5 3.75h6" />
    </svg>
    Contact Page Sections ({{ $localeName }})
</h3>

@php
    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];

    $n = function ($key) use ($locale) {
        return "contact_sections[{$locale}][{$key}]";
    };

    $v = function ($key, $default = '') use ($d) {
        return $d[$key] ?? $default;
    };

    $labels = is_array($d['labels'] ?? null) ? $d['labels'] : [];
    $placeholders = is_array($d['placeholders'] ?? null) ? $d['placeholders'] : [];
    $messages = is_array($d['messages'] ?? null) ? $d['messages'] : [];
    $methods = [];
    foreach (is_array($d['contact_methods'] ?? null) ? $d['contact_methods'] : [] as $row) {
        if (is_array($row) && !empty($row['key'])) {
            $methods[strtolower((string) $row['key'])] = $row;
        }
    }
    $methodDefinitions = [
        ['key' => 'email', 'label' => 'Email', 'hint' => 'Enter the value to generate the clickable link automatically.'],
        ['key' => 'whatsapp', 'label' => 'WhatsApp', 'hint' => 'Enter the value to generate the clickable link automatically.'],
        ['key' => 'telegram', 'label' => 'Telegram', 'hint' => 'Enter the value to generate the clickable link automatically.'],
        ['key' => 'signal', 'label' => 'Signal', 'hint' => 'Enter the value to generate the clickable link automatically.'],
        ['key' => 'teams', 'label' => 'Microsoft Teams', 'hint' => 'Enter the value to generate the clickable link automatically.'],
        ['key' => 'wechat', 'label' => 'WeChat', 'hint' => 'Enter the value to generate the clickable link automatically.'],
    ];
@endphp

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">1</span> Page Copy ({{ $localeName }})</h4>
    <div class="form-grid form-grid-2">
        <div class="form-group full-width">
            <label class="form-label">Page Title</label>
            <input type="text" name="{{ $n('page_title') }}" class="form-input"
                value="{{ old("contact_sections.{$locale}.page_title", $v('page_title', 'Contact Us')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Intro Text</label>
            <textarea name="{{ $n('page_intro') }}" rows="4" class="form-input form-textarea">{{ old("contact_sections.{$locale}.page_intro", $v('page_intro', "We're here to help. Feel free to reach out through any of the channels below or send us a message directly.")) }}</textarea>
        </div>
        <div class="form-group full-width">
            <label class="form-label">Contact Information Title</label>
            <input type="text" name="{{ $n('contact_info_title') }}" class="form-input"
                value="{{ old("contact_sections.{$locale}.contact_info_title", $v('contact_info_title', 'Contact Information')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Confirm Open Message</label>
            <input type="text" name="{{ $n('confirm_open') }}" class="form-input"
                value="{{ old("contact_sections.{$locale}.confirm_open", $v('confirm_open', 'Open this contact method?')) }}">
        </div>
    </div>

    <div class="border-t border-slate-200 pt-6 mt-6">
        <h4 class="homepage-section-heading"><span class="homepage-section-num">2</span> Contact Methods</h4>
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($methodDefinitions as $index => $definition)
                @php
                    $row = $methods[$definition['key']] ?? [];
                    $enabledValue = old("contact_sections.{$locale}.contact_methods.{$index}.enabled", $row['enabled'] ?? false);
                    $value = old(
                        "contact_sections.{$locale}.contact_methods.{$index}.value",
                        $row['value'] ?? ''
                    );
                @endphp
                <div>
                    <input type="hidden" name="{{ $n('contact_methods') }}[{{ $index }}][key]" value="{{ $definition['key'] }}">
                    <div class="flex items-center justify-between gap-4">
                        <label class="form-label mb-0">{{ $definition['label'] }}</label>
                        <label class="checkbox-label mb-0">
                            <input type="checkbox" name="{{ $n('contact_methods') }}[{{ $index }}][enabled]" value="1"
                                {{ filter_var($enabledValue, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                            Enabled
                        </label>
                    </div>
                    <input type="text" name="{{ $n('contact_methods') }}[{{ $index }}][value]" class="form-input mt-3"
                        value="{{ $value }}" placeholder="Enter the public value or handle">
                    <small class="form-hint">{{ $definition['hint'] }}</small>
                </div>
            @endforeach
        </div>

        <div class="form-group full-width mt-6">
            <label class="form-label">Target Email for Contact Form <span class="form-required">*</span></label>
            <input type="email" name="{{ $n('target_email') }}" class="form-input"
                value="{{ old("contact_sections.{$locale}.target_email", $v('target_email', $v('email', 'info@bandoskomar.org'))) }}" required>
            <small class="form-hint">This email receives submissions from the frontend contact form.</small>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-6 mt-6">
        <h4 class="homepage-section-heading"><span class="homepage-section-num">3</span> Form Copy</h4>
        <div class="form-grid form-grid-2">
            <div class="form-group">
                <label class="form-label">Form Title</label>
                <input type="text" name="{{ $n('form_title') }}" class="form-input"
                    value="{{ old("contact_sections.{$locale}.form_title", $v('form_title', 'Send Us a Message')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Success Message</label>
                <input type="text" name="{{ $n('success_message') }}" class="form-input"
                    value="{{ old("contact_sections.{$locale}.success_message", $v('success_message', 'Thank you! Your message has been sent and we will respond shortly.')) }}">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Form Subtitle</label>
                <textarea name="{{ $n('form_subtitle') }}" rows="3" class="form-input form-textarea">{{ old("contact_sections.{$locale}.form_subtitle", $v('form_subtitle', 'Fill out the form below and we will get back to you as soon as possible.')) }}</textarea>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-6 mt-6">
        <h4 class="homepage-section-heading"><span class="homepage-section-num">4</span> Text Labels</h4>
        <div class="form-grid form-grid-2">
            <div class="form-group">
                <label class="form-label">Full Name Label</label>
                <input type="text" name="{{ $n('labels') }}[full_name]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.labels.full_name", $labels['full_name'] ?? 'Full Name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Label</label>
                <input type="text" name="{{ $n('labels') }}[email_address]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.labels.email_address", $labels['email_address'] ?? 'Email Address') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Message Label</label>
                <input type="text" name="{{ $n('labels') }}[message]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.labels.message", $labels['message'] ?? 'Message') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Submit Label</label>
                <input type="text" name="{{ $n('labels') }}[send_message]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.labels.send_message", $labels['send_message'] ?? 'Send Message') }}">
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-6 mt-6">
        <h4 class="homepage-section-heading"><span class="homepage-section-num">5</span> Placeholders</h4>
        <div class="form-grid form-grid-2">
            <div class="form-group">
                <label class="form-label">Full Name Placeholder</label>
                <input type="text" name="{{ $n('placeholders') }}[full_name]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.placeholders.full_name", $placeholders['full_name'] ?? 'Your full name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Placeholder</label>
                <input type="text" name="{{ $n('placeholders') }}[email_address]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.placeholders.email_address", $placeholders['email_address'] ?? 'you@example.com') }}">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Message Placeholder</label>
                <input type="text" name="{{ $n('placeholders') }}[message]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.placeholders.message", $placeholders['message'] ?? 'How can we help you?') }}">
            </div>
            <div class="form-group full-width">
                <label class="form-label">No Methods Message</label>
                <input type="text" name="{{ $n('messages') }}[no_methods]" class="form-input"
                    value="{{ old("contact_sections.{$locale}.messages.no_methods", $messages['no_methods'] ?? 'No contact methods are available yet.') }}">
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-6 mt-6">
        <h4 class="homepage-section-heading"><span class="homepage-section-num">6</span> Contact Details</h4>
        <div class="form-grid form-grid-2">
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="{{ $n('address') }}" class="form-input"
                    value="{{ old("contact_sections.{$locale}.address", $v('address', '123 Main Street, Phnom Penh, Cambodia')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="{{ $n('phone') }}" class="form-input"
                    value="{{ old("contact_sections.{$locale}.phone", $v('phone', '+855 23 123 456')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Public Email</label>
                <input type="email" name="{{ $n('email') }}" class="form-input"
                    value="{{ old("contact_sections.{$locale}.email", $v('email', 'info@bandoskomar.org')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Office Hours</label>
                <input type="text" name="{{ $n('office_hours') }}" class="form-input"
                    value="{{ old("contact_sections.{$locale}.office_hours", $v('office_hours', 'Monday-Friday, 8:00-17:00 (ICT)')) }}">
            </div>
        </div>
    </div>
</div>
