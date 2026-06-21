<h3 class="edit-section-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
    </svg>
    About Us Page Sections ({{ $localeName }})
</h3>

@php
    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];
    $n = function ($key) use ($locale) {
        return "about_us_sections[{$locale}][{$key}]";
    };
    $v = function ($key, $default = '') use ($d) {
        return $d[$key] ?? $default;
    };

    $solIcons = $about_us_solution_icons ?? config('about_us_icons.solution_icons', []);
    $solCards = $d['solution_cards'] ?? [];
    $intCards = $d['interest_cards'] ?? [];

    $heroCards = [
        [
            'step' => '01',
            'label' => 'Impact card',
            'title_key' => 'different_subtitle',
            'description_key' => 'different_description',
            'title_default' => 'Listen locally',
            'description_default' => 'Community voices shape the priorities, programs, and follow-up support we provide.',
        ],
        [
            'step' => '02',
            'label' => 'Impact card',
            'title_key' => 'promise_subtitle',
            'description_key' => 'promise_description',
            'title_default' => 'Deliver with dignity',
            'description_default' => 'We keep the work practical, respectful, and focused on immediate needs as well as long-term outcomes.',
        ],
        [
            'step' => '03',
            'label' => 'Impact card',
            'title_key' => 'solutions_subtitle',
            'description_key' => 'solutions_description',
            'title_default' => 'Measure results',
            'description_default' => 'Transparent reporting helps supporters see where resources go and what changes because of the work.',
        ],
    ];

    $missionPrinciples = [
        [
            'label' => 'Mission',
            'title' => 'Mission title',
            'description' => 'Support children and families through education, health, and relief.',
        ],
        [
            'label' => 'Vision',
            'title' => 'Vision title',
            'description' => 'Communities where people can learn, grow, and thrive with dignity.',
        ],
        [
            'label' => 'Values',
            'title' => 'Values title',
            'description' => 'Transparency, stewardship, dignity, and collaboration guide every decision.',
        ],
    ];

    $defaultSolTitles = ['Education support', 'Health outreach', 'Emergency relief'];
    $defaultIntTitles = [
        'Meals and essentials',
        'Youth mentoring',
        'Reports and transparency',
        'Community partnerships',
        'Volunteer care',
        'Ongoing support',
    ];
@endphp

<style>
    .about-admin-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
    }

    .about-admin-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #fff;
        padding: 1rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    }

    .about-admin-card__header {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .about-admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 999px;
        background: rgba(246, 139, 30, 0.08);
        color: #1e2d53;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        padding: 0.45rem 0.8rem;
    }

    .about-admin-note {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-top: 0.35rem;
    }

    .about-admin-preview {
        margin-top: 1rem;
        border-left: 3px solid #f68b1e;
        background: #fff8ef;
        color: #7c4a0d;
        border-radius: 14px;
        padding: 0.9rem 1rem;
        font-size: 0.88rem;
        line-height: 1.55;
    }

    .about-admin-stack {
        display: grid;
        gap: 1rem;
    }

    .about-admin-card .form-grid {
        margin-top: 0;
    }

    .about-admin-wide {
        grid-column: span 12;
    }

    .about-admin-half {
        grid-column: span 6;
    }

    .about-admin-third {
        grid-column: span 4;
    }

    @media (max-width: 900px) {
        .about-admin-half,
        .about-admin-third {
            grid-column: span 12;
        }
    }
</style>

<div class="about-admin-stack">
    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">1</span>
                    Hero section
                </h4>
                <p class="about-admin-note">
                    Controls the main heading and lead text at the top of the About page.
                </p>
            </div>
            <span class="about-admin-badge">{{ $localeName }}</span>
        </div>

        <div class="about-admin-grid">
            <div class="form-group about-admin-wide">
                <label class="form-label">Eyebrow text</label>
                <input type="text" name="{{ $n('results_subtitle') }}" class="form-input"
                    value="{{ old($n('results_subtitle'), $v('results_subtitle', 'How we create impact')) }}">
            </div>
            <div class="form-group about-admin-wide">
                <label class="form-label">Hero title</label>
                <input type="text" name="{{ $n('results_title') }}" class="form-input"
                    value="{{ old($n('results_title'), $v('results_title', 'Practical support that lasts beyond a single donation')) }}">
            </div>
            <div class="form-group about-admin-wide">
                <label class="form-label">Hero description</label>
                <textarea name="{{ $n('results_description') }}" rows="4" class="form-input form-textarea">{{ old($n('results_description'), $v('results_description', 'Bandos Komar works with local leaders, families, and volunteers to make support visible, accountable, and useful over time.')) }}</textarea>
            </div>
        </div>
    </div>

    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">2</span>
                    Image strip
                </h4>
                <p class="about-admin-note">
                    A row of image-only tiles shown near the top of the About page.
                </p>
            </div>
        </div>

        <div class="about-admin-grid">
            <div class="form-group about-admin-wide">
                @include('admin.system-management.pages.partials.homepage-image-upload', [
                    'name' => "about_us_sections[{$locale}][about_strip_image_files][]",
                    'label' => 'Upload strip images',
                    'multiple' => true,
                    'keepImagesName' => 'about_keep_strip_images',
                    'stagedPurpose' => 'about_strip',
                    'currentImages' => $v('about_strip_images', []),
                    'currentListLabel' => 'Current strip images',
                    'hint' => 'These images appear only as tiles on the About page. Upload a few images for the best layout.',
                ])
            </div>
        </div>
    </div>

    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">3</span>
                    Three hero cards
                </h4>
                <p class="about-admin-note">
                    These cards appear beside the hero and introduce the page story in three steps.
                </p>
            </div>
        </div>

        <div class="about-admin-grid">
            @foreach ($heroCards as $index => $card)
                @php
                    $spanClass = $index === 2 ? 'about-admin-wide' : 'about-admin-half';
                @endphp
                <div class="about-admin-card {{ $spanClass }}">
                    <div class="about-admin-card__header">
                        <div class="about-admin-badge">{{ $card['step'] }}</div>
                        <span class="about-admin-note">{{ $card['label'] }}</span>
                    </div>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Card title</label>
                            <input type="text" name="{{ $n($card['title_key']) }}" class="form-input"
                                value="{{ old($n($card['title_key']), $v($card['title_key'], $card['title_default'])) }}">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Card description</label>
                            <textarea name="{{ $n($card['description_key']) }}" rows="3" class="form-input form-textarea">{{ old($n($card['description_key']), $v($card['description_key'], $card['description_default'])) }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">4</span>
                    Main story section
                </h4>
                <p class="about-admin-note">
                    This is the large story block with the image on the About page.
                </p>
            </div>
        </div>

        <div class="about-admin-grid">
            <div class="form-group about-admin-wide">
                <label class="form-label">Section title</label>
                <input type="text" name="{{ $n('different_title') }}" class="form-input"
                    value="{{ old($n('different_title'), $v('different_title', 'A community-first NGO with clear accountability')) }}">
            </div>
            <div class="form-group about-admin-wide">
                <label class="form-label">Section description</label>
                <textarea name="{{ $n('different_description') }}" rows="4" class="form-input form-textarea">{{ old($n('different_description'), $v('different_description', 'We exist to strengthen education, family wellbeing, and emergency response through local partnerships and steady follow-through.')) }}</textarea>
            </div>
            <div class="form-group about-admin-wide">
                <label class="form-label">Highlight text</label>
                <input type="text" name="{{ $n('different_check') }}" class="form-input"
                    value="{{ old($n('different_check'), $v('different_check', 'Community-led support')) }}">
            </div>
            <div class="form-group about-admin-wide">
                @include('admin.system-management.pages.partials.homepage-image-upload', [
                    'name' => "about_us_sections[{$locale}][different_image_file]",
                    'pathName' => $n('different_image'),
                    'label' => 'Section image',
                    'pathValue' => $v('different_image', ''),
                    'currentImageUrl' => $v('different_image'),
                    'uploadPath' => 'images/about-us',
                    'sharedImageKey' => 'about_different_image',
                    'hint' => 'Used in the large story panel on the About page.',
                ])
            </div>
        </div>
    </div>

    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">5</span>
                    Mission, vision, values cards
                </h4>
                <p class="about-admin-note">
                    These three cards sit inside the main story block on the About page.
                </p>
            </div>
        </div>

        <div class="about-admin-grid">
            @foreach ([1, 2, 3] as $i)
                @php
                    $cardDefaults = [
                        1 => ['label' => 'Mission', 'title' => 'Mission', 'description' => 'Support children and families through education, health, and relief.'],
                        2 => ['label' => 'Vision', 'title' => 'Vision', 'description' => 'Communities where people can learn, grow, and thrive with dignity.'],
                        3 => ['label' => 'Values', 'title' => 'Values', 'description' => 'Transparency, stewardship, dignity, and collaboration guide every decision.'],
                    ];
                    $card = $cardDefaults[$i];
                @endphp
                <div class="about-admin-card about-admin-third">
                    <div class="about-admin-card__header">
                        <div class="about-admin-badge">0{{ $i }}</div>
                        <span class="about-admin-note">{{ $card['label'] }}</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Title</label>
                            <input type="text" name="{{ $n('mission_' . $i . '_title') }}" class="form-input"
                                value="{{ old($n('mission_' . $i . '_title'), $v('mission_' . $i . '_title', $card['title'])) }}">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea name="{{ $n('mission_' . $i . '_description') }}" rows="3" class="form-input form-textarea">{{ old($n('mission_' . $i . '_description'), $v('mission_' . $i . '_description', $card['description'])) }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">6</span>
                    Program cards
                </h4>
                <p class="about-admin-note">
                    These are the three program cards shown on the About page.
                </p>
            </div>
        </div>

        <div class="about-admin-grid">
            <div class="form-group about-admin-wide">
                <label class="form-label">Section subtitle</label>
                <input type="text" name="{{ $n('solutions_subtitle') }}" class="form-input"
                    value="{{ old($n('solutions_subtitle'), $v('solutions_subtitle', 'Programs shaped for lasting change')) }}">
            </div>
            <div class="form-group about-admin-wide">
                <label class="form-label">Section title</label>
                <input type="text" name="{{ $n('solutions_title') }}" class="form-input"
                    value="{{ old($n('solutions_title'), $v('solutions_title', 'Programs designed for lasting change')) }}">
            </div>
            <div class="form-group about-admin-wide">
                <label class="form-label">Section description</label>
                <textarea name="{{ $n('solutions_description') }}" rows="3" class="form-input form-textarea">{{ old($n('solutions_description'), $v('solutions_description', 'Each initiative is shaped to respond to community needs with practical support and local partnership.')) }}</textarea>
            </div>
        </div>

        <div class="about-admin-grid mt-4">
            @foreach ([1, 2, 3] as $i)
                @php $solCard = $solCards[$i - 1] ?? []; @endphp
                <div class="about-admin-card about-admin-third">
                    <div class="about-admin-card__header">
                        <div class="about-admin-badge">0{{ $i }}</div>
                        <span class="about-admin-note">Program {{ $i }}</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Title</label>
                            <input type="text" name="{{ $n('solution_' . $i . '_title') }}" class="form-input"
                                value="{{ old($n('solution_' . $i . '_title'), $solCard['title'] ?? $defaultSolTitles[$i - 1]) }}">
                        </div>
                        <div class="form-group full-width">
                            @include('admin.system-management.pages.partials.homepage-image-upload', [
                                'name' => "about_us_sections[{$locale}][solution_{$i}_image_file]",
                                'pathName' => $n('solution_' . $i . '_image'),
                                'label' => 'Card image',
                                'pathValue' => $solCard['image'] ?? '',
                                'currentImageUrl' => $solCard['image'] ?? '',
                                'uploadPath' => 'images/about-us',
                                'sharedImageKey' => 'about_solution_' . $i . '_image',
                                'hint' => 'Shown as the image for this program card.',
                                'fullCard' => true,
                            ])
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea name="{{ $n('solution_' . $i . '_description') }}" rows="3" class="form-input form-textarea">{{ old($n('solution_' . $i . '_description'), $solCard['description'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">7</span>
                    Support areas
                </h4>
                <p class="about-admin-note">
                    These cards appear in the lower section of the About page.
                </p>
            </div>
        </div>

        <div class="about-admin-grid">
            <div class="form-group about-admin-wide">
                <label class="form-label">Section title</label>
                <input type="text" name="{{ $n('interests_title') }}" class="form-input"
                    value="{{ old($n('interests_title'), $v('interests_title', 'Where support matters most')) }}">
            </div>
        </div>

        <div class="about-admin-grid mt-4">
            @foreach ([1, 2, 3, 4, 5, 6] as $i)
                @php $card = $intCards[$i - 1] ?? []; @endphp
                <div class="about-admin-card about-admin-third">
                    <div class="about-admin-card__header">
                        <div class="about-admin-badge">0{{ $i }}</div>
                        <span class="about-admin-note">Support card {{ $i }}</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Title</label>
                            <input type="text" name="{{ $n('interest_' . $i . '_title') }}" class="form-input"
                                value="{{ old($n('interest_' . $i . '_title'), $card['title'] ?? $defaultIntTitles[$i - 1]) }}">
                        </div>
                        <div class="form-group full-width">
                        @include('admin.system-management.pages.partials.homepage-image-upload', [
                            'name' => "about_us_sections[{$locale}][interest_{$i}_image_file]",
                            'pathName' => $n('interest_' . $i . '_image'),
                            'label' => 'Card image',
                            'pathValue' => $card['image'] ?? '',
                            'currentImageUrl' => $card['image'] ?? '',
                            'uploadPath' => 'images/about-us',
                            'sharedImageKey' => 'about_interest_' . $i . '_image',
                            'hint' => 'Shown as the visual for this support card.',
                            'fullCard' => true,
                        ])
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea name="{{ $n('interest_' . $i . '_description') }}" rows="3" class="form-input form-textarea">{{ old($n('interest_' . $i . '_description'), $card['description'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="about-admin-card">
        <div class="about-admin-card__header">
            <div>
                <h4 class="homepage-section-heading">
                    <span class="homepage-section-num">8</span>
                    CTA section
                </h4>
                <p class="about-admin-note">
                    Bottom banner text. The description is now editable too.
                </p>
            </div>
        </div>

        <div class="about-admin-grid">
            <div class="form-group about-admin-wide">
                <label class="form-label">CTA title</label>
                <input type="text" name="{{ $n('ready_title') }}" class="form-input"
                    value="{{ old($n('ready_title'), $v('ready_title', 'Support the work, share the story, or ask how to get involved.')) }}">
            </div>
            <div class="form-group about-admin-wide">
                <label class="form-label">CTA description</label>
                <textarea name="{{ $n('ready_description') }}" rows="3" class="form-input form-textarea">{{ old($n('ready_description'), $v('ready_description', 'About Us is stronger when donors, volunteers, and partners move together with the communities we serve.')) }}</textarea>
            </div>
        </div>
    </div>
    
</div>
