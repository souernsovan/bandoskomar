<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Editable content definitions for every website page.
 *
 * Each page lists sections of fields. The admin editor is generated from these
 * definitions, and the frontend falls back to the "default" values until an
 * admin saves their own content.
 *
 * Translation model: English holds everything. Other languages only store text
 * (text / textarea / rich). Pictures, links, numbers and the items of each list
 * always come from English; list text is translated item by item.
 */
class ContentSchema
{
    /** Slug of the hidden page that stores header, footer and shared blocks. */
    public const SITE = 'site';

    public const TRANSLATABLE = ['text', 'textarea', 'rich'];

    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif'];

    public const FILE_EXTENSIONS = ['pdf'];

    public const ICONS = [
        'book' => 'Book',
        'leaf' => 'Leaf',
        'shield' => 'Shield',
        'target' => 'Target',
        'eye' => 'Eye',
        'users' => 'People',
        'home' => 'House',
        'heart' => 'Heart',
        'pin' => 'Location pin',
        'mail' => 'Email',
        'phone' => 'Phone',
        'clock' => 'Clock',
        'calendar' => 'Calendar',
        'chart' => 'Chart',
    ];

    private const WP = 'https://www.bandoskomar.org/wp-content/uploads/2025/10/';

    private static ?array $pages = null;

    public static function has(?string $slug): bool
    {
        return $slug !== null && isset(self::pages()[$slug]);
    }

    public static function for(?string $slug): ?array
    {
        return $slug !== null ? (self::pages()[$slug] ?? null) : null;
    }

    /** Flat map of key => field definition for a page. */
    public static function fields(?string $slug): array
    {
        $fields = [];
        foreach (self::for($slug)['sections'] ?? [] as $section) {
            $fields += $section['fields'];
        }

        return $fields;
    }

    public static function isTranslatable(array $field): bool
    {
        return in_array($field['type'], self::TRANSLATABLE, true);
    }

    /** Whether a list has any text that can be translated. */
    public static function listIsTranslatable(array $field): bool
    {
        foreach ($field['fields'] as $sub) {
            if (self::isTranslatable($sub)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clean submitted values for one locale, uploading any new files.
     *
     * @param  callable(UploadedFile, string): string  $upload  returns the stored public path
     */
    public static function sanitize(string $slug, string $locale, array $input, array $files, callable $upload): array
    {
        $isBase = $locale === PageLocales::default();
        $result = [];

        foreach (self::fields($slug) as $key => $field) {
            if ($field['type'] === 'list') {
                if (! $isBase && ! self::listIsTranslatable($field)) {
                    continue;
                }
                $rows = [];
                foreach ((array) ($input[$key] ?? []) as $index => $row) {
                    $row = is_array($row) ? $row : [];
                    $item = [];
                    foreach ($field['fields'] as $subKey => $sub) {
                        if (! $isBase && ! self::isTranslatable($sub)) {
                            continue;
                        }
                        $item[$subKey] = self::cleanValue($sub, $row[$subKey] ?? null, $files[$key][$index][$subKey] ?? null, $upload);
                    }
                    $rows[] = $item;
                }
                $result[$key] = $rows;

                continue;
            }

            if (! $isBase && ! self::isTranslatable($field)) {
                continue;
            }
            $result[$key] = self::cleanValue($field, $input[$key] ?? null, $files[$key] ?? null, $upload);
        }

        return $result;
    }

    private static function cleanValue(array $field, mixed $value, mixed $file, callable $upload): mixed
    {
        return match ($field['type']) {
            'checkbox' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? $value + 0 : 0,
            'image', 'file' => $file instanceof UploadedFile ? $upload($file, $field['type']) : trim((string) $value),
            'select', 'icon' => array_key_exists((string) $value, self::options($field)) ? (string) $value : (string) ($field['default'] ?? ''),
            'textarea', 'rich' => trim(str_replace("\r\n", "\n", (string) $value)),
            default => trim((string) $value),
        };
    }

    public static function options(array $field): array
    {
        return $field['type'] === 'icon' ? self::ICONS : ($field['options'] ?? []);
    }

    // ------------------------------------------------------------------
    // Field builders
    // ------------------------------------------------------------------

    private static function text(string $label, string $default = '', array $extra = []): array
    {
        return ['type' => 'text', 'label' => $label, 'default' => $default] + $extra;
    }

    private static function textarea(string $label, string $default = '', array $extra = []): array
    {
        return ['type' => 'textarea', 'label' => $label, 'default' => $default] + $extra;
    }

    /** Text that may contain <mark>, <strong>, <em>, <br> and <a>. */
    private static function rich(string $label, string $default = ''): array
    {
        return ['type' => 'rich', 'label' => $label, 'default' => $default,
            'hint' => 'Wrap words in <mark>…</mark> to highlight them in orange.'];
    }

    private static function image(string $label, string $default = ''): array
    {
        return ['type' => 'image', 'label' => $label, 'default' => $default];
    }

    private static function file(string $label, string $default = ''): array
    {
        return ['type' => 'file', 'label' => $label, 'default' => $default, 'hint' => 'Upload a PDF or paste a link.'];
    }

    private static function link(string $label, string $default = ''): array
    {
        return ['type' => 'url', 'label' => $label, 'default' => $default,
            'hint' => 'A page path like /donate, or a full link like https://…'];
    }

    private static function number(string $label, int|float $default = 0): array
    {
        return ['type' => 'number', 'label' => $label, 'default' => $default];
    }

    private static function checkbox(string $label, bool $default = true): array
    {
        return ['type' => 'checkbox', 'label' => $label, 'default' => $default];
    }

    private static function select(string $label, array $options, string $default): array
    {
        return ['type' => 'select', 'label' => $label, 'options' => $options, 'default' => $default];
    }

    private static function icon(string $label, string $default): array
    {
        return ['type' => 'icon', 'label' => $label, 'default' => $default];
    }

    private static function items(string $label, string $itemLabel, array $fields, array $default): array
    {
        return ['type' => 'list', 'label' => $label, 'item_label' => $itemLabel, 'fields' => $fields, 'default' => $default];
    }

    private static function banner(string $image): array
    {
        return [
            'title' => 'Page banner',
            'fields' => [
                'banner_image' => self::image('Banner background image', self::WP.$image),
                'banner_title' => self::text('Banner title', '', ['hint' => 'Leave empty to use the page title.']),
            ],
        ];
    }

    private static function giveToggle(): array
    {
        return ['show_give' => self::checkbox('Show the "Donate" band at the bottom (edit its text in the "site" page)')];
    }

    private static function imageRows(array $files): array
    {
        return array_map(fn ($f) => ['image' => self::WP.$f], $files);
    }

    private static function textRows(array $lines): array
    {
        return array_map(fn ($t) => ['text' => $t], $lines);
    }

    // ------------------------------------------------------------------
    // Shared default content
    // ------------------------------------------------------------------

    private const ABOUT_TEXT = 'Bandos Komar (BK) is a local NGO dedicated to <mark>improving education</mark> in Cambodia, especially in rural areas. The organization originated from Partage, which began operating in Cambodia in November <mark>1989</mark>, initially providing basic support and progressively developing projects focused on education—particularly in pre-schools and primary schools in rural areas and surrounding villages. From <mark>1989</mark> to <mark>1999</mark>, the program supported six primary and six pre-schools in Takeo, Kompong Speu, and Kandal provinces.';

    private const SELECTION_TEXT = 'Bandos Komar ensures <mark>fair and transparent</mark> selection by working closely with communities to identify children and youth most in need. We prioritize those from <mark>low-income</mark> families, at risk of dropping out, or facing social challenges. Through household visits and local participation, we make sure support reaches the right beneficiaries and creates long-term impact. Our approach empowers communities and strengthens <mark>education</mark> systems across Cambodia.';

    private const SKILLS_TEXT = 'Our Life Skills program <mark>empowers</mark> children with practical abilities, confidence, and a sense of <mark>responsibility</mark>—helping them thrive in their families, communities, and future careers.';

    private const CONTEXT_POINTS = [
        'Thousands of children and youth reached through education and development programs.',
        'Expanded support across 10 provinces with strong local partnerships in Cambodia.',
        'Our programs began in the 1990s and expanded to Pursat and Siem Reap.',
        'Our goal is to build resilient, inclusive communities for sustainable development.',
        'We empower children and youth through education, life skills, and protection programs.',
    ];

    private const GALLERY_CATEGORIES = ['education' => 'Education', 'lifeskills' => 'Life Skills', 'community' => 'Community'];

    private static function skillTiles(): array
    {
        return [
            ['image' => self::WP.'komar.png', 'title' => 'Practical Agriculture', 'text' => 'Children participate in our “School Gardening” project, learning to plant, nurture, and harvest crops.'],
            ['image' => self::WP.'490504848_979551997646287_5088911998051210915_n.jpg', 'title' => 'Career Preparation', 'text' => 'Our training helps students develop essential skills for future employment.'],
            ['image' => self::WP.'image-20.png', 'title' => 'Community Engagement', 'text' => 'Students and teachers work together on meaningful projects.'],
            ['image' => self::WP.'image-23.png', 'title' => 'Personal Growth', 'text' => 'Children gain confidence, responsibility, and a positive attitude toward work and community life.'],
        ];
    }

    private static function tileFields(): array
    {
        return ['image' => self::image('Image'), 'title' => self::text('Title'), 'text' => self::textarea('Text')];
    }

    private static function cardFields(): array
    {
        return ['icon' => self::icon('Icon', 'book'), 'title' => self::text('Title'), 'text' => self::textarea('Text')];
    }

    private static function stepFields(): array
    {
        return ['title' => self::text('Title'), 'text' => self::textarea('Text')];
    }

    private static function galleryRows(array $files): array
    {
        return array_map(fn ($pair) => ['image' => self::WP.$pair[0], 'category' => $pair[1], 'caption' => ''], $files);
    }

    private static function galleryFields(): array
    {
        return [
            'image' => self::image('Image'),
            'category' => self::select('Category', ['' => 'No category'] + self::GALLERY_CATEGORIES, ''),
            'caption' => self::text('Caption (optional)'),
        ];
    }

    private const ACTIVITY_IMAGES = [
        ['photo_2025-07-17_08-49-15-300x200.jpg', 'education'], ['image-24-266x300.png', 'community'],
        ['image-22-300x230.png', 'education'], ['Container-1-265x300.png', 'lifeskills'],
        ['333-300x169.jpg', 'community'], ['5555-300x200.jpg', 'education'],
        ['03-300x200.jpg', 'lifeskills'], ['4444-300x225.jpg', 'community'],
    ];

    // ------------------------------------------------------------------
    // Page definitions
    // ------------------------------------------------------------------

    public static function pages(): array
    {
        return self::$pages ??= [
            self::SITE => self::site(),
            'home' => self::home(),
            'about-us' => self::about(),
            'history' => self::history(),
            'product' => self::program(),
            'jobs-announcement' => self::jobs(),
            'annual-report' => self::annualReport(),
            'strategic-plan' => self::strategicPlan(),
            'partner' => self::partner(),
            'volunteer' => self::volunteer(),
            'image-gallery' => self::gallery(),
            'video' => self::video(),
            'contact' => self::contact(),
            'donate' => self::donate(),
        ];
    }

    private static function site(): array
    {
        $link = fn ($label, $url) => ['label' => $label, 'url' => $url];
        $linkFields = ['label' => self::text('Label'), 'url' => self::link('Link')];

        return ['label' => 'Website: header, footer & shared blocks', 'sections' => [
            ['title' => 'Logo & header', 'fields' => [
                'logo' => self::image('Logo', 'images/logo/bandos-komar-logo.png'),
                'site_title' => self::text('Name shown in the browser tab', 'Bandos Komar Organization'),
                'meta_description' => self::textarea('Default search engine description', 'Bandos Komar (BK) is a local NGO dedicated to improving education in Cambodia, especially in rural areas.'),
                'home_label' => self::text('"Home" label in breadcrumbs', 'Home', ['default_km' => 'ទំព័រដើម']),
                'resources_label' => self::text('Menu group: Info & Resources', 'Info & Resources', ['default_km' => 'ព័ត៌មាន និងធនធាន']),
                'involved_label' => self::text('Menu group: Get Involved', 'Get Involved', ['default_km' => 'ចូលរួមជាមួយយើង']),
                'donate_label' => self::text('Donate button label', 'Donate', ['default_km' => 'បរិច្ចាគ']),
            ]],
            ['title' => 'Contact details (header menu, footer, contact page)', 'fields' => [
                'address' => self::text('Address', '#123 Street, Phnom Penh, Cambodia'),
                'email' => self::text('Public email', 'admin@bandoskomar.org'),
                'phone' => self::text('Phone', '+855 23 456 789'),
                'office_hours' => self::text('Office hours', 'Monday – Friday, 8:00 – 17:00'),
                'form_email' => self::text('Send website form messages to this email', 'admin@bandoskomar.org',
                    ['hint' => 'Contact, volunteer, job and donation forms are emailed here.']),
                'form_error' => self::text('Message when a form cannot be sent', 'Sorry, we could not send your form. Please email us directly.'),
                'facebook_url' => self::link('Facebook page', 'https://www.facebook.com/BandosKomarAssociation/'),
                'youtube_url' => self::link('YouTube channel', 'https://www.youtube.com/@bandoskomar'),
            ]],
            ['title' => 'Footer', 'fields' => [
                'footer_about' => self::textarea('About text', 'Welcome to Bandos Komar, where brilliance meets innovation! We are a leading NGO dedicated to delivering exceptional services to support children and communities in Cambodia.'),
                'footer_col1_title' => self::text('Column 1 title', 'Sitemap'),
                'footer_col1_links' => self::items('Column 1 links', 'Link', $linkFields, [
                    $link('History', '/history'), $link('About', '/about-us'), $link('Contact', '/contact'),
                    $link('Programs & Approach', '/programs'), $link('Human Resources', '/page/jobs-announcement'),
                ]),
                'footer_col2_title' => self::text('Column 2 title', 'Info & Resources'),
                'footer_col2_links' => self::items('Column 2 links', 'Link', $linkFields, [
                    $link('Jobs Announcement', '/page/jobs-announcement'), $link('Annual Report', '/page/annual-report'),
                    $link('Strategic Plan', '/page/strategic-plan'), $link('Partner', '/page/partner'), $link('Audio & Visual', '/page/video'),
                ]),
                'footer_col3_title' => self::text('Column 3 title', 'Get Involved'),
                'footer_col3_links' => self::items('Column 3 links', 'Link', $linkFields, [
                    $link('Volunteer', '/page/volunteer'), $link('Image', '/gallery'), $link('Video', '/page/video'),
                ]),
                'footer_contact_title' => self::text('Contact column title', 'Contact'),
                'copyright' => self::text('Copyright line', 'All Rights Reserved. Website Designed & Supported By PNC-VC2-2025'),
            ]],
            ['title' => 'Donate band (bottom of many pages)', 'fields' => [
                'give_title' => self::text('Title', 'We empower children and youth through education, life skills, and protection programs.'),
                'give_text' => self::text('Text', '$25 can give a child what they need to stay in school.'),
            ]],
            ['title' => 'Campaign progress bar (home & donate pages)', 'fields' => [
                'campaign_title' => self::text('Campaign title', 'Back to School Campaign 2026–2027'),
                'campaign_text' => self::textarea('Campaign text', 'Help us keep children from low-income families in the classroom this school year.'),
                'campaign_raised' => self::number('Amount raised (USD)', 12450),
                'campaign_goal' => self::number('Goal (USD)', 20000),
                'campaign_raised_label' => self::text('"raised" label', 'raised'),
                'campaign_goal_label' => self::text('"Goal" label', 'Goal:'),
                'campaign_reached_label' => self::text('"reached" label', 'reached'),
            ]],
            ['title' => 'Donate popup', 'fields' => [
                'popup_enabled' => self::checkbox('Show the popup (once per visit)'),
                'popup_delay' => self::number('Show after this many seconds', 20),
                'popup_image' => self::image('Image', self::WP.'komama.jpg'),
                'popup_title' => self::text('Title', 'Help a child stay in school'),
                'popup_text' => self::textarea('Text', 'Children from low-income families in rural Cambodia are at risk of dropping out. A small gift today keeps them learning.'),
                'popup_amounts' => self::items('Quick amounts (USD)', 'Amount', ['amount' => self::number('Amount')],
                    [['amount' => 10], ['amount' => 25], ['amount' => 50]]),
                'popup_later' => self::text('"Maybe later" label', 'Maybe later'),
            ]],
        ]];
    }

    private static function home(): array
    {
        return ['label' => 'Home page', 'sections' => [
            ['title' => 'Hero slider', 'fields' => [
                'hero_slides' => self::items('Slides', 'Slide', ['image' => self::image('Image')],
                    self::imageRows(['sub-banner.jpg', 'Banner1-1-1.png', 'file_1E8CF86F-6B95-4E1F-A666-3AB39FD72388.png'])),
                'hero_kicker' => self::text('Small title above', 'Bandos Komar Association'),
                'hero_title' => self::text('Main title', 'Every child deserves to stay in school.'),
                'hero_text' => self::rich('Text', 'Bandos Komar (BK) is a local NGO dedicated to <mark>improving education</mark> in Cambodia, especially in rural areas.'),
                'hero_button_2_label' => self::text('Second button label', 'Our Program'),
                'hero_button_2_url' => self::link('Second button link', '/programs'),
            ]],
            ['title' => 'Numbers', 'fields' => [
                'stats' => self::items('Numbers', 'Number', [
                    'number' => self::number('Number'),
                    'suffix' => self::text('After the number (e.g. "s" or "+")'),
                    'plain' => self::checkbox('Show without thousands separator (for years)', false),
                    'label' => self::text('Label'),
                ], [
                    ['number' => 1989, 'suffix' => '', 'plain' => true, 'label' => 'Supporting children since'],
                    ['number' => 10, 'suffix' => '', 'plain' => false, 'label' => 'Provinces in Cambodia'],
                    ['number' => 12, 'suffix' => '', 'plain' => false, 'label' => 'Primary & pre-schools supported (1989–1999)'],
                    ['number' => 1000, 'suffix' => 's', 'plain' => false, 'label' => 'Children and youth reached'],
                ]),
            ]],
            ['title' => 'The problem', 'fields' => [
                'problem_image' => self::image('Image', self::WP.'komama.jpg'),
                'problem_caption' => self::text('Image caption', 'Many children in rural Cambodia are at risk of leaving school too early.'),
                'problem_title' => self::text('Title', 'Without help, many children never finish school.'),
                'problem_text' => self::rich('Text', 'In rural villages, children from <mark style="color:#fff">low-income</mark> families are often pulled out of class to help their families earn a living. Once they leave, most never come back.'),
                'problem_points' => self::items('Numbered points', 'Point', ['text' => self::text('Text')], self::textRows([
                    'Families cannot afford uniforms, books, and school supplies.',
                    'Children at risk of dropping out need support to stay in class.',
                    'Young people need practical life skills to find work and support their families.',
                ])),
                'problem_button' => self::text('Button label', 'Help a child stay in school'),
            ]],
            ['title' => 'About & campaign', 'fields' => [
                'about_title' => self::text('Title', 'Bandos Komar Association'),
                'about_text' => self::rich('Text', self::ABOUT_TEXT),
                'about_button' => self::text('Button label', 'History'),
                'about_button_url' => self::link('Button link', '/history'),
                'show_campaign' => self::checkbox('Show the campaign progress box (edit it in the "site" page)', false),
            ]],
            ['title' => 'Country context', 'fields' => [
                'context_image' => self::image('Image', self::WP.'komar.png'),
                'context_title' => self::text('Title', 'Country Context'),
                'context_points' => self::items('Points', 'Point', ['text' => self::text('Text')], self::textRows(self::CONTEXT_POINTS)),
            ]],
            ['title' => 'Selection process', 'fields' => [
                'selection_title' => self::text('Title', 'Our Selection Process'),
                'selection_text' => self::rich('Text', self::SELECTION_TEXT),
                'selection_quote' => self::textarea('Quote', 'Your support reaches the children who need it most—chosen fairly, with their own communities.'),
                'selection_images' => self::items('Images (3)', 'Image', ['image' => self::image('Image')],
                    self::imageRows(['komama.jpg', 'photo_2025-07-25_09-07-30.png', 'image-7.png'])),
            ]],
            ['title' => 'Life skills', 'fields' => [
                'skills_title' => self::text('Title', 'Life Skills'),
                'skills_text' => self::rich('Text', self::SKILLS_TEXT),
                'skills_tiles' => self::items('Tiles', 'Tile', self::tileFields(), self::skillTiles()),
            ]],
            ['title' => 'Completed activities', 'fields' => [
                'activities_title' => self::text('Title', 'Our Completed Activities'),
                'activities_images' => self::items('Images', 'Image', self::galleryFields(), self::galleryRows(self::ACTIVITY_IMAGES)),
                'activities_button' => self::text('Button label', 'Image'),
                'activities_button_url' => self::link('Button link', '/gallery'),
            ]],
            ['title' => 'Final call to action', 'fields' => [
                'cta_image' => self::image('Background image', self::WP.'sub-banner.jpg'),
                'cta_title' => self::text('Title', 'A child is waiting for your help today.'),
                'cta_text' => self::textarea('Text', 'Your gift gives a child in rural Cambodia the chance to learn, grow, and build a better future for their family.'),
                'cta_button' => self::text('Button label', 'Donate now'),
                'cta_trust_1' => self::text('Trust note 1', 'Our team will contact you'),
                'cta_trust_2' => self::text('Trust note 2', 'Results shared in our Annual Report'),
            ]],
        ]];
    }

    private static function about(): array
    {
        return ['label' => 'About page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Introduction', 'fields' => [
                'intro_title' => self::text('Title', 'Bandos Komar Association'),
                'intro_text' => self::rich('Text', self::ABOUT_TEXT),
                'intro_image' => self::image('Image', self::WP.'komama.jpg'),
            ]],
            ['title' => 'Our foundation: vision & mission', 'fields' => [
                'foundation_title' => self::text('Title', 'Our Foundation'),
                'cards' => self::items('Cards', 'Card', self::cardFields(), [
                    ['icon' => 'eye', 'title' => 'Vision', 'text' => 'Every children and youth enjoy full development potential with dignity and sustainability.'],
                    ['icon' => 'target', 'title' => 'Mission', 'text' => 'The mission of Bandos Komar is to support the human resource development and livelihoods improvement enabling participation opportunities of the communities, public institutions, civil society, the private sector and other relevant stakeholders.'],
                ]),
            ]],
            ['title' => 'Core values', 'fields' => [
                'values_title' => self::text('Title', 'Core Values'),
                'values_text' => self::textarea('Text', 'Guiding principles in our work.'),
                'values' => self::items('Values', 'Value', self::stepFields(), [
                    ['title' => 'Child-Centered', 'text' => 'We prioritize the best interests of every child.'],
                    ['title' => 'Inclusiveness', 'text' => 'We ensure access for all children, regardless of background.'],
                    ['title' => 'Empowerment', 'text' => 'We build the capacity of local communities and teachers.'],
                    ['title' => 'Sustainability', 'text' => 'We promote long-term, community-driven change.'],
                    ['title' => 'Integrity', 'text' => 'We act with transparency, respect, and accountability.'],
                ]),
            ]],
            ['title' => 'Our story (picture + text blocks)', 'fields' => [
                'story' => self::items('Blocks', 'Block', self::tileFields(), [
                    ['image' => self::WP.'4444.jpg', 'title' => 'A gateway to a better life through inclusive education and empowerment', 'text' => 'Bandos Komar Organization empowers underprivileged children and youth in Cambodia through education, life skills, and community support. We focus on child protection, youth development, and partnerships with families and communities—especially in rural areas. Our mission is to help young people grow with dignity, gain confidence, and actively shape a better, more inclusive future.'],
                    ['image' => self::WP.'6666.jpg', 'title' => 'Inclusive opportunities for children and youth in Cambodia', 'text' => 'We ensure that children and youth, especially those from disadvantaged backgrounds, can reach their full potential. Bandos Komar Organization provides access to quality education, life skills, and strong community support to help them grow with confidence and dignity. Our programs emphasize child protection, youth empowerment, and collaboration with families, communities, and public institutions across rural Cambodia—building a more sustainable and inclusive future.'],
                ]),
            ]],
            ['title' => 'Country context', 'fields' => [
                'context_image' => self::image('Image', self::WP.'komar.png'),
                'context_title' => self::text('Title', 'Country Context'),
                'context_points' => self::items('Points', 'Point', ['text' => self::text('Text')], self::textRows(self::CONTEXT_POINTS)),
            ]],
            ['title' => 'Country strategic goal', 'fields' => [
                'goal_title' => self::text('Title', 'Country Strategic Goal'),
                'goal_text' => self::textarea('Text', '“By 2019, children and youths enjoy full potential of their rights in living with dignity to become human capital for sustainable development of the society”.'),
                'goal_tiles' => self::items('Goal areas', 'Area', self::tileFields(), [
                    ['image' => self::WP.'image-19.png', 'title' => 'Economic Development', 'text' => 'Fostering sustainable economic growth through innovation and strategic partnerships.'],
                    ['image' => self::WP.'image-27.png', 'title' => 'Social Progress', 'text' => 'Building inclusive communities and improving quality of life for all citizens.'],
                    ['image' => self::WP.'file_C89D8536-93E6-4067-B66A-8B280F67EC1B.png', 'title' => 'Environmental Sustainability', 'text' => 'Protecting our natural resources while promoting green technology and practices.'],
                ]),
                'show_partners' => self::checkbox('Show the partner logos (edit them on the Partner page)'),
                'partners_title' => self::text('Partner logos title', 'Our Partners'),
            ] + self::giveToggle()],
        ]];
    }

    private static function history(): array
    {
        $event = fn ($year, $image, $title, $text) => ['year' => $year, 'image' => self::WP.$image, 'title' => $title, 'text' => $text];

        return ['label' => 'History page', 'sections' => [
            self::banner('Banner1-1-1.png'),
            ['title' => 'Introduction', 'fields' => [
                'intro_title' => self::text('Title', 'Bandos Komar History'),
                'intro_text' => self::rich('Text', 'Bandos Komar has been empowering Cambodian children and communities through education and child rights programs since <mark>1989</mark>.'),
            ]],
            ['title' => 'Timeline', 'fields' => [
                'timeline' => self::items('Timeline', 'Event', [
                    'year' => self::text('Date / label'), 'image' => self::image('Image (optional)'),
                    'title' => self::text('Title'), 'text' => self::textarea('Text'),
                ], [
                    $event('1989', 'komar.png', 'Partage Education Programs Begin', 'In 1989, Partage, a French NGO, initiated education programs in Cambodia, focusing on pre-schools and primary schools in the rural provinces of Takeo, Kampong Speu, and Kandal. This marked the beginning of efforts to improve educational access for Cambodian children.'),
                    $event('1999', 'komama.jpg', 'Localization and Formal Launch', 'In July 1999, Partage’s programs were localized into the Bandos Komar Association, a fully Cambodian NGO. The official launch in August 1999 marked a significant step toward local ownership, continuing the mission to support education and child welfare.'),
                    $event('2002', 'Bandos-Komar-org_2-1.jpg', 'Expansion to Pursat Province', 'In 2002, Bandos Komar extended its educational and community development programs to Pursat Province, broadening its impact to support more children in rural areas with access to quality education and early childhood care.'),
                    $event('2008', 'Library.jpg', 'Expansion to Siem Reap Province', 'In 2008, Bandos Komar further expanded its reach by launching programs in Siem Reap Province. This expansion strengthened the organization’s commitment to improving access to education and child welfare in rural Cambodian communities.'),
                    $event('2017', 'image-7.png', 'Target expansion strategy', 'In 2017, Bandos Komar expanded its reach from 5 to 10 provinces, extending support to children in need and those pursuing their education.'),
                    $event('2023', 'image-24.png', 'Contributions to Education and Health', 'In 2023, Bandos Komar contributed to building educational facilities, such as school fences and classroom floor tiling for children. It also shared knowledge with teachers and parents about childcare, and took part in sharing knowledge on healthcare and prenatal care.'),
                ]),
            ]],
            ['title' => 'Our legacy today', 'fields' => [
                'legacy_title' => self::text('Title', 'Our Legacy Today'),
                'legacy_text' => self::textarea('Text', 'Three decades of dedication have built a foundation for lasting change in Cambodia.'),
                'legacy_stats' => self::items('Numbers', 'Number', [
                    'number' => self::text('Number'), 'label' => self::text('Label'), 'text' => self::textarea('Details'),
                ], [
                    ['number' => '10', 'label' => 'Provinces Served', 'text' => 'Takeo, Kampong Speu, Kandal, Pursat, Siem Reap, Ratanakiri, Preah Sihanouk, Kampong Thom, Kampong Chhnang'],
                    ['number' => '36', 'label' => 'Years of Service', 'text' => 'Longstanding commitment to development'],
                ]),
            ]],
            ['title' => 'Photos', 'fields' => [
                'gallery' => self::items('Photos', 'Photo', self::galleryFields(), self::galleryRows([
                    ['komama.jpg', ''], ['photo_2025-07-25_09-07-30.png', ''],
                    ['image-7.png', ''], ['490504848_979551997646287_5088911998051210915_n.jpg', ''],
                ])),
            ] + self::giveToggle()],
        ]];
    }

    private static function program(): array
    {
        $topic = fn ($program, $image, $title, $text) => ['program' => $program, 'image' => $image, 'title' => $title, 'text' => $text];
        $netlify = 'https://delightful-profiterole-f8aef0.netlify.app/image/';

        return ['label' => 'Our Program page', 'sections' => [
            self::banner('file_1E8CF86F-6B95-4E1F-A666-3AB39FD72388.png'),
            ['title' => 'Introduction', 'fields' => [
                'intro_title' => self::text('Title', 'Our Program'),
                'intro_text' => self::textarea('Text', 'We empower children and youth through education, life skills, and protection programs.'),
                'cards' => self::items('Summary cards (optional)', 'Card', self::cardFields(), []),
            ]],
            ['title' => 'Programs', 'fields' => [
                'programs' => self::items('Programs', 'Program', [
                    'code' => self::text('Short code (used to match topics, e.g. IECCD)'),
                    'title' => self::text('Name'),
                    'goal' => self::textarea('Goal'),
                ], [
                    ['code' => 'IECCD', 'title' => 'Integrated Early Childhood Care and Development Program (IECCD)', 'goal' => 'By 2029, boys and girls under 6 years old who are beneficiaries will receive care and development with potential and opportunities to continue their education at the primary level with quality, equity and inclusion education.'],
                    ['code' => 'IQBE', 'title' => 'Integrated Quality Basic Education Program (IQBE)', 'goal' => 'By 2029, girls and boys aged 6-15 in target areas have access to education with quality learning outcome and complete basic education with equity and inclusiveness.'],
                    ['code' => 'S4P', 'title' => 'Skills For Professional Program (S4P)', 'goal' => 'By 2029, young women and men in the target areas of Bandos Komar will be equipped with market-relevant skills to gain access to decent jobs and entrepreneurship opportunities to escape poverty.'],
                ]),
                'program_topics' => self::items('Program topics', 'Topic', [
                    'program' => self::text('Program code (e.g. IECCD)'),
                    'image' => self::image('Image'),
                    'title' => self::text('Title'),
                    'text' => self::textarea('Text (one point per line)'),
                ], [
                    $topic('IECCD', $netlify.'2024/SAM~1.JPG', 'Health and Nutrition', 'Skilled birth attendance rose from 96% to 99% (2014–2022). Exclusive breastfeeding rates climbed from 11% (2000) to 74% (2010) but dropped to 51% by 2021–22. Malnutrition remains high: 22% of children under 5 are stunted, 22% underweight, and 16% wasting.'),
                    $topic('IECCD', self::WP.'cleaning-water.jpg', 'Clean Water & Sanitation', 'Access to safe water remains a major challenge. 20% of schools lack water service entirely, and 37% have no sanitation facilities—posing serious health and hygiene risks for children.'),
                    $topic('IECCD', $netlify.'2024/%E1%9E%94%E1%9F%92%E1%9E%9A%E1%9E%87%E1%9E%BB%E1%9F%86%E1%9E%87%E1%9E%B6%E1%9F%A1%E1%9E%80%E1%9E%BB%E1%9E%98%E1%9E%B6%E1%9E%9A%E1%9E%8F%E1%9F%86%E1%9E%8E%E1%9E%B6%E1%9E%84%E1%9E%80%E1%9E%B6%E1%9E%9A%E1%9E%A2%E1%9E%97%E1%9E%B7%E1%9E%9C%E1%9E%8C%E1%9F%92%E1%9E%8D%E1%9E%93%E1%9F%8F%E1%9F%A1.jpg', 'Early Learning Challenges', 'Many children fall behind in school due to poor early learning. Only 12% of 3-year-olds, 28% of 4-year-olds, and 60% of 5-year-olds access early education. Irregular attendance and poor teaching quality remain critical issues.'),
                    $topic('IECCD', $netlify.'15/IMG_0018.JPG', 'Caregiver Knowledge', 'Around 73% of children under age 3 are cared for by grandmothers with limited knowledge of early childhood development, highlighting the need for broader caregiver education and support.'),
                    $topic('IQBE', $netlify.'2024/%E1%9E%8A%E1%9F%86%E1%9E%8E%E1%9E%BE%E1%9E%9A%E1%9E%91%E1%9E%9F%E1%9F%92%E1%9E%9F%E1%9E%93%E1%9F%88%E1%9E%80%E1%9E%B7%E1%9E%85%E1%9F%92%E1%9E%85%E1%9E%9A%E1%9E%94%E1%9E%9F%E1%9F%8B%E1%9E%98%E1%9F%92%E1%9E%85%E1%9E%B6%E1%9E%9F%E1%9F%8B%E1%9E%87%E1%9F%86%E1%9E%93%E1%9E%BD%E1%9E%99%E1%9E%93%E1%9F%85%E1%9E%9F%E1%9E%B6%E1%9E%9B%E1%9E%B6%E1%9E%98%E1%9E%8F%E1%9F%92%E1%9E%8F%E1%9F%81%E1%9E%99%E1%9F%92%E1%9E%99%E1%9E%9F%E1%9E%B7%E1%9E%80%E1%9F%92%E1%9E%9F%E1%9E%B6%E1%9E%9F%E1%9E%A0%E1%9E%82%E1%9E%98%E1%9E%93%E1%9F%8D%E1%9E%A2%E1%9E%BC%E1%9E%9A%E1%9E%8F%E1%9F%92%E1%9E%9A%E1%9E%87%E1%9E%B6%E1%9E%80%E1%9F%8B%E1%9E%85%E1%9E%B7%E1%9E%8F%E1%9F%92%E1%9E%8F.jpg', 'Quality of learning in basic education', "The percentage of secondary schools with safe water was 79.5% in 2022-2023 and 89.4% in 2023-2024 school years.\nMoEYS developed Children’s Councils in schools to disseminate child rights and leadership skills; only 14.2% were functioning in 2018/19.\nYouth councils existed in 506 schools (~26%), but many were inactive due to unclear support mechanisms and poor local motivation.\n(Source: Analysis of the situation of children and adolescents in Cambodia 2023, UNICEF)"),
                    $topic('IQBE', $netlify.'2024/%E1%9E%96%E1%9E%B7%E1%9E%92%E1%9E%B8%E1%9E%85%E1%9F%82%E1%9E%80%E1%9E%9C%E1%9E%B7%E1%9E%89%E1%9F%92%E1%9E%89%E1%9E%B6%E1%9E%94%E1%9E%93%E1%9E%94%E1%9E%8F%E1%9F%92%E1%9E%9A%E1%9E%94%E1%9E%89%E1%9F%92%E1%9E%87%E1%9E%B6%E1%9E%80%E1%9F%8B%E1%9E%80%E1%9E%B6%E1%9E%9A%E1%9E%9F%E1%9E%B7%E1%9E%80%E1%9F%92%E1%9E%9F%E1%9E%B6%E1%9E%87%E1%9E%BC%E1%9E%93%E1%9E%9F%E1%9E%B7%E1%9E%9F%E1%9F%92%E1%9E%9F%E1%9E%98%E1%9E%8F%E1%9F%92%E1%9E%8F%E1%9F%81%E1%9E%99%E1%9F%92%E1%9E%99%E1%9E%A2%E1%9E%B6%E1%9E%99%E1%9E%BB%E1%9F%A5%E1%9E%86%E1%9F%92%E1%9E%93%E1%9E%B6.jpg', 'Equal Access to quality in education', "53% have upper secondary certificates; 23% hold a bachelor’s degree; 19% have a lower secondary certificate.\nPrimary school completion rate is 89.2% (Male: 86.3%, Female: 92.3%).\nLower secondary school completion rate is 60.97% (Male: 55.69%, Female: 66.37%).\nPrimary education transition rate is 86.6% (Male: 81.4%, Female: 89%).\nDropout rate at grade 6 is 13.1%, increasing to 28.8% at grade 9.\n1,725 primary schools have standard libraries out of 7,398 schools nationwide."),
                    $topic('S4P', self::WP.'511276811_1145708517593196_5372775329216584370_n-1.jpg', 'Challenges Facing Youth', "Youth entering the workforce are often unprepared with low education levels.\nLess than half of youth (18–24) completed lower secondary school; nearly 1 in 4 dropped out after primary school (22.3%).\nVery few access continuing education or TVET courses.\nMost youth work in low-skilled, informal employment (67%).\nHalf are unpaid family workers without benefits or social protection.\n12.7% of youths were not in employment, education, or training (NEET)."),
                    $topic('S4P', self::WP.'513484003_1145708577593190_4816091177279255430_n.jpg', 'Strategic Issues', "Lack of access to career orientation and guidance for students and youth.\nLimited awareness of TVET importance creates barriers to skills access.\nLack of parental support due to cultural norms or family expectations.\nInsufficient private sector support affects youth employment.\nPoor training quality impacts TVET program success."),
                ]),
            ]],
            ['title' => 'Life skills', 'fields' => [
                'skills_title' => self::text('Title', 'Life Skills'),
                'skills_text' => self::rich('Text', self::SKILLS_TEXT),
                'skills_tiles' => self::items('Tiles', 'Tile', self::tileFields(), self::skillTiles()),
            ]],
            ['title' => 'Selection process', 'fields' => [
                'selection_title' => self::text('Title', 'Our Selection Process'),
                'selection_text' => self::rich('Text', self::SELECTION_TEXT),
                'selection_images' => self::items('Images (3)', 'Image', ['image' => self::image('Image')],
                    self::imageRows(['komama.jpg', 'photo_2025-07-25_09-07-30.png', 'image-7.png'])),
                'steps' => self::items('Steps', 'Step', self::stepFields(), [
                    ['title' => 'Work with communities', 'text' => 'We work closely with communities to identify children and youth most in need.'],
                    ['title' => 'Household visits', 'text' => "Our team visits families to understand each child's situation."],
                    ['title' => 'Prioritize need', 'text' => 'Children from low-income families, at risk of dropping out, or facing social challenges come first.'],
                    ['title' => 'Support & follow-up', 'text' => 'Support reaches the right beneficiaries and creates long-term impact.'],
                ]),
            ]],
            ['title' => 'Projects', 'fields' => [
                'show_projects' => self::checkbox('Show the Programs (Products) list from the admin', false),
                'projects_title' => self::text('Title (Programs list)', 'Our projects'),
                'projects_button' => self::text('Project button label', 'Read more'),
            ] + self::giveToggle()],
        ]];
    }

    private static function jobs(): array
    {
        return ['label' => 'Jobs Announcement page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Job list', 'fields' => [
                'intro_title' => self::text('Title', 'Join our team'),
                'intro_text' => self::textarea('Text', 'Help us improve education for children and youth in rural Cambodia. Open positions are listed below.'),
                'jobs' => self::items('Jobs', 'Job', [
                    'title' => self::text('Position'),
                    'location' => self::text('Location'),
                    'type' => self::text('Type (e.g. Full-time)'),
                    'deadline' => self::text('Deadline'),
                    'status' => self::select('Status', ['open' => 'Open', 'closed' => 'Closed'], 'open'),
                    'pdf' => self::file('Job description PDF (optional)'),
                ], []),
                'open_label' => self::text('"Open" tag', 'Open'),
                'closed_label' => self::text('"Closed" tag', 'Closed'),
                'apply_button' => self::text('"Apply now" button', 'Apply now'),
                'details_button' => self::text('"Job description" button', 'Job description'),
                'no_jobs_text' => self::textarea('Text when there are no jobs', 'There are no open positions at the moment. Please check back soon, or send us your CV through the contact page.'),
            ]],
            ['title' => 'Application form', 'fields' => [
                'apply_title' => self::text('Title', 'Apply for a position'),
                'apply_text' => self::textarea('Text', 'Send your CV and cover letter. Only shortlisted candidates will be contacted.'),
                'label_name' => self::text('Name label', 'Full name'),
                'label_email' => self::text('Email label', 'Email'),
                'label_phone' => self::text('Phone label', 'Phone'),
                'label_position' => self::text('Position label', 'Position'),
                'position_placeholder' => self::text('Position placeholder', 'Select position'),
                'label_cv' => self::text('CV label', 'CV (PDF)'),
                'label_letter' => self::text('Cover letter label', 'Cover letter'),
                'submit_label' => self::text('Submit button', 'Send application'),
                'success_message' => self::text('Success message', 'Application sent. We will contact you if you are shortlisted.'),
            ]],
        ]];
    }

    private static function annualReport(): array
    {
        $report = fn ($y) => ['year' => (string) $y, 'title' => "Annual Report $y", 'text' => "Programs, results, and finances for $y.", 'pdf' => ''];

        return ['label' => 'Annual Report page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Reports', 'fields' => [
                'intro_title' => self::text('Title', 'Our yearly results'),
                'intro_text' => self::textarea('Text', 'Each annual report shows what we did, who we reached, and how funds were used.'),
                'cover_label' => self::text('Text on the report cover', 'Annual Report'),
                'download_label' => self::text('Download button', 'Download PDF'),
                'soon_label' => self::text('Label when no PDF is uploaded', 'Coming soon'),
                'reports' => self::items('Reports', 'Report', [
                    'year' => self::text('Year'), 'title' => self::text('Title'), 'text' => self::textarea('Text'), 'pdf' => self::file('PDF'),
                ], [$report(2025), $report(2024), $report(2023), $report(2022)]),
            ] + self::giveToggle()],
        ]];
    }

    private static function strategicPlan(): array
    {
        return ['label' => 'Strategic Plan page', 'sections' => [
            self::banner('Banner1-1-1.png'),
            ['title' => 'Introduction', 'fields' => [
                'intro_title' => self::text('Title', 'Strategic Plan 2025 – 2029'),
                'intro_text' => self::textarea('Text (one paragraph per line)', 'Our goal is to build resilient, inclusive communities for sustainable development.'),
                'plan_pdf' => self::file('Strategic plan PDF'),
                'download_label' => self::text('Download button', 'Download Strategic Plan (PDF)'),
                'intro_image' => self::image('Image', self::WP.'image-23.png'),
            ]],
            ['title' => 'Plan sections', 'fields' => [
                'blocks' => self::items('Sections', 'Section', [
                    'title' => self::text('Title'), 'text' => self::textarea('Text'), 'image' => self::image('Picture / infographic'),
                ], [
                    ['title' => 'Core Values of Bandos Komar Association', 'text' => 'The Core Values of Bandos Komar Association guide all our actions and programs. We focus on protecting children’s rights, promoting equality, respecting dignity, encouraging creativity, ensuring accountability, and building strong partnerships. With resilience, flexibility, gender responsiveness, and a result-based approach, we work for lasting positive change in communities.', 'image' => self::WP.'ppppp-1.jpg'],
                    ['title' => 'Strategic Choices', 'text' => 'The Strategic Choices outline the core directions and priorities that guide program planning, decision-making, and implementation. They serve as a framework to ensure resources are used effectively, partnerships are strengthened, and the intended impact is achieved in a sustainable way.', 'image' => self::WP.'image.jpeg'],
                ]),
            ]],
            ['title' => 'Program goals 2025 – 2029', 'fields' => [
                'goals_title' => self::text('Title', 'Program goals by 2029'),
                'goals' => self::items('Goals', 'Goal', self::cardFields(), [
                    ['icon' => 'heart', 'title' => 'Early Childhood Care and Development (IECCD)', 'text' => 'Children under 6 receive care and development with opportunities to continue their education at primary level with quality, equity and inclusion.'],
                    ['icon' => 'book', 'title' => 'Quality Basic Education (IQBE)', 'text' => 'Girls and boys aged 6–15 in target areas access quality education and complete basic education with equity and inclusiveness.'],
                    ['icon' => 'leaf', 'title' => 'Skills For Professional (S4P)', 'text' => 'Young women and men gain market-relevant skills to access decent jobs and entrepreneurship opportunities to escape poverty.'],
                ]),
            ]],
            ['title' => 'Photos', 'fields' => [
                'photos' => self::items('Photos', 'Photo', self::galleryFields(), self::galleryRows([
                    ['ooooo.jpg', ''], ['uuuu.jpg', ''], ['lllllllllll.jpg', ''],
                    ['nj.jpg', ''], ['pp.jpg', ''], ['oo.jpg', ''],
                    ['ppppp.jpg', ''], ['ll.jpg', ''], ['iooo.jpg', ''], ['kkkkk.jpg', ''],
                ])),
            ] + self::giveToggle()],
        ]];
    }

    private static function partner(): array
    {
        $logo = 'https://samounsuon.github.io/ourPartnerBondoskomar/image/';
        $p = fn ($title, $file) => ['logo' => $logo.$file, 'mono' => '', 'title' => $title, 'text' => '', 'url' => ''];

        return ['label' => 'Partner page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Partners', 'fields' => [
                'intro_title' => self::text('Title', 'Our Partners'),
                'intro_text' => self::textarea('Text', 'Expanded support across 10 provinces with strong local partnerships in Cambodia.'),
                'partners' => self::items('Partners', 'Partner', [
                    'logo' => self::image('Logo (optional)'),
                    'mono' => self::text('Letters shown when there is no logo'),
                    'title' => self::text('Name'),
                    'text' => self::textarea('Text (optional)'),
                    'url' => self::link('Website (optional)'),
                ], [
                    $p('Plan International', 'Plan.png'), $p('Don du Choeur', 'Don%20du%20choeur.png'),
                    $p('Partage', 'Partage.png'), $p('World Vision', 'World%20Vision.png'),
                    $p('Johanniter', 'Johanniter.png'), $p('Action Education', 'action%20Education.png'),
                    $p('Aide et Action', 'Aide%20et%20Action.png'), $p('AFD', 'AFD.png'),
                    $p('BSDA', 'BSDA.png'), $p('Cquest', 'cquest.png'),
                    $p('Planete EED', 'planete.png'), $p('World Bank', 'world-bank.png'),
                    $p('World Education', 'world%20education.png'), $p('European Union', 'european.png'),
                    $p('Japan Government', 'japangov.png'),
                ]),
                'button_label' => self::text('Button label', 'Become a partner'),
                'button_url' => self::link('Button link', '/contact'),
            ]],
            ['title' => 'Sponsors activity', 'fields' => [
                'activity_title' => self::text('Title', 'Sponsors Activity'),
                'activity_images' => self::items('Photos', 'Photo', self::galleryFields(), self::galleryRows([
                    ['uuuu.jpg', ''], ['ppppp-1.jpg', ''], ['kkkkkk.jpg', ''], ['kkkkk.jpg', ''],
                    ['ppppp.jpg', ''], ['oo.jpg', ''], ['nj.jpg', ''], ['511276811_1145708517593196_5372775329216584370_n.jpg', ''],
                    ['cleaning-water.jpg', ''], ['file_590FB0EE-4D17-41EC-A313-341AB827C20B.png', ''], ['Library.jpg', ''],
                    ['Bandos-Komar-org_2-1.jpg', ''], ['6666.jpg', ''], ['7777.jpg', ''],
                    ['photo_2025-10-30_17-21-34.jpg', ''], ['file_31D1D781-2524-41B6-8329-923151A1805F-1.png', ''],
                ])),
            ]],
        ]];
    }

    private static function volunteer(): array
    {

        return ['label' => 'Volunteer page', 'sections' => [
            self::banner('file_1E8CF86F-6B95-4E1F-A666-3AB39FD72388.png'),
            ['title' => 'Introduction', 'fields' => [
                'intro_title' => self::text('Title', 'Volunteer with us'),
                'intro_text' => self::textarea('Text', 'Share your time and skills with children and youth in Cambodia. Volunteers help with teaching, life skills activities, school gardening, and community events.'),
                'intro_points' => self::items('Points', 'Point', ['text' => self::text('Text')], self::textRows([
                    'Support teachers in pre-schools and primary schools',
                    'Help run life skills and career preparation workshops',
                    'Join school gardening and community projects',
                    'Help with photos, video, and communication',
                ])),
                'intro_image' => self::image('Image', self::WP.'490504848_979551997646287_5088911998051210915_n.jpg'),
            ]],
            ['title' => 'Our volunteers', 'fields' => [
                'team_title' => self::text('Title', 'Meet our volunteers'),
                'team_text' => self::textarea('Text', 'People from Cambodia and around the world who give their time and skills to help children learn and grow.'),
                'all_label' => self::text('"All" filter label', 'All'),
                'categories' => self::items('Filter buttons', 'Filter', [
                    'key' => self::text('Code (no spaces, used to match volunteers)'), 'label' => self::text('Label'),
                ], [
                    ['key' => 'teaching', 'label' => 'Teaching'], ['key' => 'training', 'label' => 'Training'],
                    ['key' => 'lifeskills', 'label' => 'Life Skills'], ['key' => 'gardening', 'label' => 'Gardening'],
                    ['key' => 'media', 'label' => 'Media'],
                ]),
                'volunteers' => self::items('Volunteers', 'Volunteer', [
                    'name' => self::text('Name'),
                    'role' => self::text('Role'),
                    'category' => self::text('Filter code (e.g. teaching)'),
                    'location' => self::text('Location'),
                    'duration' => self::text('Duration'),
                    'photo' => self::image('Photo (optional, initials are shown otherwise)'),
                    'text' => self::textarea('Description'),
                ], []),
                'read_more' => self::text('"Read more" label', 'Read more'),
                'show_less' => self::text('"Show less" label', 'Show less'),
            ]],
            ['title' => 'How to become a volunteer', 'fields' => [
                'steps_title' => self::text('Title', 'How to become a volunteer'),
                'steps' => self::items('Steps', 'Step', self::stepFields(), [
                    ['title' => 'Apply online', 'text' => 'Fill in the form below with your details and interests.'],
                    ['title' => 'Talk with our team', 'text' => 'We contact you by email to discuss your skills and availability.'],
                    ['title' => 'Orientation', 'text' => 'Learn about our programs, child protection policy, and the communities we work with.'],
                    ['title' => 'Start helping', 'text' => 'Join activities in schools and communities with our local staff.'],
                ]),
            ]],
            ['title' => 'Application form', 'fields' => [
                'apply_title' => self::text('Title', 'Apply to volunteer'),
                'apply_text' => self::textarea('Text', 'Fill in the form and our team will contact you about available opportunities.'),
                'label_name' => self::text('Name label', 'Full name'),
                'label_email' => self::text('Email label', 'Email'),
                'label_phone' => self::text('Phone label', 'Phone'),
                'label_availability' => self::text('Availability label', 'Availability'),
                'availability_options' => self::items('Availability options', 'Option', ['text' => self::text('Text')],
                    self::textRows(['1–2 weeks', '1 month', '3 months or more', 'Weekends only'])),
                'label_interest' => self::text('Interest label', 'Area of interest'),
                'interest_options' => self::items('Interest options', 'Option', ['text' => self::text('Text')],
                    self::textRows(['Teaching support', 'Life skills workshops', 'School gardening', 'Media & communication'])),
                'label_message' => self::text('Message label', 'Tell us about yourself'),
                'submit_label' => self::text('Submit button', 'Send application'),
                'success_message' => self::text('Success message', 'Thank you! We received your volunteer application and will contact you soon.'),
            ]],
        ]];
    }

    private static function gallery(): array
    {
        $files = ['image-20.png', 'image-19.png', 'file_31D1D781-2524-41B6-8329-923151A1805F-2.png',
            'file_11ABA6A4-2F6F-48DA-A0AE-FF7FB524DC17-2.png', 'file_0131C0CE-6CF7-413D-BD92-00BE3B59E4F5-2.png',
            'file_84163706-CA75-4CD5-928C-DF0D30275D3C-2.png', 'file_25514D8A-3F17-4228-A213-6E4861FB580F-2.png',
            'image-22.png', 'image-7.png', 'Container-1.png', '5555.jpg', '6666.jpg', '4444.jpg', '7777.jpg',
            'photo_2025-07-25_09-07-30.png', '513484003_1145708577593190_4816091177279255430_n.jpg',
            'photo_2025-07-25_09-11-27.jpg', '490504848_979551997646287_5088911998051210915_n.jpg',
            '03.jpg', '01.jpg', '02.jpg', 'file_B8300CF8-6F82-4ABB-865C-E3243F997C91.png',
            'file_E9BB6BF7-E855-48A1-BA18-F253FBE4D240.png', 'file_C89D8536-93E6-4067-B66A-8B280F67EC1B.png',
            'file_695DE50A-0342-4DAB-8BD8-3850F5BC5B5E.png'];

        return ['label' => 'Image gallery page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Gallery', 'fields' => [
                'title' => self::text('Title', 'Our Image in Bandos Komar Organization'),
                'all_label' => self::text('"All" filter label', 'All'),
                'education_label' => self::text('"Education" filter label', 'Education'),
                'lifeskills_label' => self::text('"Life Skills" filter label', 'Life Skills'),
                'community_label' => self::text('"Community" filter label', 'Community'),
                'images' => self::items('Images (filters appear once images have categories)', 'Image', self::galleryFields(),
                    self::galleryRows(array_map(fn ($f) => [$f, ''], $files))),
            ]],
        ]];
    }

    private static function video(): array
    {
        $vid = fn ($title, $id) => ['title' => $title, 'thumbnail' => "https://i.ytimg.com/vi/$id/hqdefault.jpg", 'video_url' => "https://www.youtube.com/watch?v=$id"];

        return ['label' => 'Video page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Videos', 'fields' => [
                'intro_title' => self::text('Title', 'Our Video'),
                'intro_text' => self::textarea('Text', 'Stories from our education, life skills, and community programs.'),
                'videos' => self::items('Videos', 'Video', [
                    'title' => self::text('Title'),
                    'thumbnail' => self::image('Thumbnail image'),
                    'video_url' => self::link('YouTube or Vimeo link'),
                ], [
                    $vid('Bandos Komar Organization', 'UizLZDLvr9c'),
                    $vid('Bandos Komar', 'SgMstzUIKpE'),
                ]),
                'no_video_text' => self::text('Text when a video has no link yet', 'This video is coming soon.'),
                'channel_label' => self::text('YouTube channel button (link set in the "site" page)', 'Watch more on our YouTube channel'),
            ]],
        ]];
    }

    private static function contact(): array
    {
        return ['label' => 'Contact page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Contact information', 'fields' => [
                'intro_title' => self::text('Title', 'Contact us'),
                'intro_text' => self::textarea('Text', 'Bandos Komar is a Cambodian NGO working to support children, youth, and communities through quality education, life skills, and sustainable development. Get in touch with us using the form below!'),
                'label_address' => self::text('"Address" label', 'Address'),
                'label_email' => self::text('"Email" label', 'Email'),
                'label_phone' => self::text('"Phone" label', 'Phone'),
                'label_hours' => self::text('"Office hours" label', 'Office hours'),
                'social_title' => self::text('Social media title', 'Get in touch directly and on our social media'),
            ]],
            ['title' => 'Contact form', 'fields' => [
                'label_name' => self::text('Name label', 'Full name'),
                'label_form_email' => self::text('Email label', 'Email'),
                'label_subject' => self::text('Subject label', 'Subject'),
                'subjects' => self::items('Subject options', 'Subject', ['text' => self::text('Text')],
                    self::textRows(['General question', 'Donation', 'Partnership', 'Volunteer', 'Jobs'])),
                'label_message' => self::text('Message label', 'Message'),
                'submit_label' => self::text('Submit button', 'Send message'),
                'success_message' => self::text('Success message', 'Message sent. We will reply to your email soon.'),
            ]],
            ['title' => 'Our location', 'fields' => [
                'location_title' => self::text('Title', 'Our Location'),
                'location_text' => self::textarea('Text', 'Bandos Komar is here to help! Visit our offices in Phnom Penh and Siem Reap to learn more about our programs and how we support children, youth, and families in local communities.'),
                'offices' => self::items('Offices', 'Office', [
                    'name' => self::text('Name'),
                    'map_url' => self::link('Google Maps embed link (Share → Embed a map → copy the src="…" link)'),
                ], [
                    ['name' => 'Phnom Penh', 'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59457.24070527355!2d104.80509334863282!3d11.514709500000006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3109511e7a409b83%3A0xa54896247da13e13!2sBandos%20Komar%20Phnom%20Penh!5e1!3m2!1sen!2skh!4v1753754444643!5m2!1sen!2skh'],
                    ['name' => 'Siem Reap', 'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29516.9418005989!2d103.83095763085666!3d13.37016190068923!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311017674ecbffc5%3A0x371d08a0883ec3c!2sBandos%20Komar%20Association!5e1!3m2!1sen!2skh!4v1753754752357!5m2!1sen!2skh'],
                ]),
            ]],
        ]];
    }

    private static function donate(): array
    {
        $cause = fn ($image, $title, $text) => ['image' => self::WP.$image, 'title' => $title, 'text' => $text];

        return ['label' => 'Donate page', 'sections' => [
            self::banner('sub-banner.jpg'),
            ['title' => 'Why give', 'fields' => [
                'intro_title' => self::text('Title', 'Contribute Today'),
                'intro_text' => self::rich('Text', 'Transform lives with love by supporting Bandos Komar’s dedicated efforts in <mark>child welfare</mark>, <mark>equitable education</mark>, and <mark>sustainable community development</mark> across Cambodia for over three decades!'),
                'impact' => self::items('What each amount does (optional)', 'Amount', [
                    'amount' => self::number('Amount (USD)'), 'text' => self::text('Text'),
                ], []),
                'show_campaign' => self::checkbox('Show the campaign progress box (edit it in the "site" page)', false),
                'quote' => self::textarea('Quote', '“Our approach empowers communities and strengthens education systems across Cambodia.”'),
            ]],
            ['title' => 'Causes you support', 'fields' => [
                'causes_title' => self::text('Title', 'Where your support goes'),
                'causes' => self::items('Causes', 'Cause', self::tileFields(), [
                    $cause('7777.jpg', 'Support Early Childhood Education', 'Help Bandos Komar provide access to quality preschool programs in rural Cambodian communities.'),
                    $cause('Library.jpg', 'Improve Primary Education', 'Support teacher training, school materials, and better learning environments for Cambodian children.'),
                    $cause('file_590FB0EE-4D17-41EC-A313-341AB827C20B.png', 'Promote Child Health & Nutrition', 'Contribute to programs that raise awareness on hygiene, nutrition, and healthcare access for students.'),
                    $cause('photo_2025-07-25_09-08-15-1.png', 'Empower Local Communities', 'Bandos Komar works with families and local leaders to create sustainable child-friendly communities.'),
                    $cause('file_11ABA6A4-2F6F-48DA-A0AE-FF7FB524DC17-1.png', 'Make Donations', 'Help us fund vital programs for food, education, and healthcare.'),
                    $cause('Bandos-Komar-org_2-1.jpg', 'We Need Volunteers', 'Contribute your time and skills to support our missions.'),
                ]),
            ]],
            ['title' => 'Donation form', 'fields' => [
                'form_title' => self::text('Title', 'Donation form'),
                'form_text' => self::textarea('Text', 'Choose an amount and send. Our team will contact you by email with the next steps.'),
                'label_amount' => self::text('Amount label', 'Amount (USD)'),
                'amounts' => self::items('Amount buttons (USD)', 'Amount', ['amount' => self::number('Amount')],
                    [['amount' => 10], ['amount' => 25], ['amount' => 50], ['amount' => 100]]),
                'default_amount' => self::number('Amount selected at start', 25),
                'label_custom' => self::text('Custom amount label', 'Or enter your own amount'),
                'label_name' => self::text('Name label', 'Full name'),
                'label_email' => self::text('Email label', 'Email'),
                'label_phone' => self::text('Phone label', 'Phone (optional)'),
                'label_message' => self::text('Message label', 'Message (optional)'),
                'message_placeholder' => self::text('Message placeholder', 'Anything you would like to tell us'),
                'total_label' => self::text('Total label', 'Your donation'),
                'no_amount_message' => self::text('Message when no amount is chosen', 'Please choose or enter an amount.'),
                'submit_label' => self::text('Submit button', 'Send donation'),
                'success_message' => self::text('Success message (:amount is replaced)', 'Thank you! Your donation of $:amount was sent. Our team will contact you by email soon.'),
                'trust_1' => self::text('Trust note 1', 'Our team will contact you'),
                'trust_2' => self::text('Trust note 2', 'See how funds are used'),
                'trust_2_url' => self::link('Trust note 2 link', '/page/annual-report'),
            ]],
        ]];
    }
}
