<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\PageLocales;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $siteName = SiteSetting::get('site_name', 'Community Care Foundation');
        $siteDescription = SiteSetting::get(
            'site_description',
            'Community-led non-profit supporting education, health, and relief programs.'
        );
        $siteLogoUrl = asset(SiteSetting::siteLogoPath());
        $currentMaxSortOrder = Page::max('sort_order') ?? 0;
        Page::where('slug', 'impact-areas')->delete();
        $targetSlugs = [
            'home',
            'platform',
            'about-us',
            'product',
            'history',
            'jobs-announcement',
            'annual-report',
            'strategic-plan',
            'partner',
            'volunteer',
            'image-gallery',
            'image',
            'video',
            'contact',
            'donate',
        ];

        // Move the existing seeded pages out of the way first so the predefined
        // sort orders can be restored without hitting the unique index.
        $temporarySortOrder = $currentMaxSortOrder + 1000;
        foreach ($targetSlugs as $slug) {
            $existing = Page::where('slug', $slug)->first();
            if ($existing) {
                $existing->update(['sort_order' => $temporarySortOrder++]);
            }
        }

        $pages = [
            [
                'slug' => 'home',
                'title' => 'Home',
                'content' => 'Building stronger communities together.',
                'route_name' => 'frontend.home',
                'meta_title' => $siteName . ' | Building stronger communities together',
                'meta_description' => $siteDescription,
                'og_tags' => [
                    'og_title' => $siteName . ' | Building stronger communities together',
                    'og_description' => $siteDescription,
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.home'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'url' => route('frontend.home'),
                    'description' => $siteDescription,
                    'logo' => $siteLogoUrl,
                ],
                'sort_order' => 1,
                'page_content' => $this->localizedContent([
                    'hero_headline' => 'Building stronger communities together.',
                    'hero_description' => 'We are a non-profit organization dedicated to education, health, and community support. Together with volunteers and donors, we turn compassion into action.',
                    'hero_image' => '',
                    'company_title' => 'Our Mission',
                    'company_description' => 'We mobilize people, resources, and partnerships to create practical support for communities that need it most.',
                    'company_logo' => '',
                    'value_prop_1_title' => 'Education',
                    'value_prop_1_desc' => 'Scholarships, tutoring, school supplies, and learning spaces that help children and young people thrive.',
                    'value_prop_2_title' => 'Health & Care',
                    'value_prop_2_desc' => 'Community health outreach, wellness education, and compassionate care for families.',
                    'value_prop_3_title' => 'Emergency Relief',
                    'value_prop_3_desc' => 'Fast response support for families facing crisis, displacement, or urgent hardship.',
                    'capabilities_image' => '',
                    'marketing_title' => 'Get involved',
                    'marketing_description' => 'Donate, volunteer, or partner with us to help expand our impact across more communities.',
                    'marketing_image' => '',
                    'mobile_title' => 'Impact in action',
                    'mobile_image' => '',
                    'mobile_bg' => '',
                    'style_title' => 'Featured programs',
                    'color_choice_title' => 'Program areas',
                    'styles' => [],
                    'partners_title' => 'Our supporters',
                    'partner_images' => [],
                ]),
            ],
            [
                'slug' => 'platform',
                'title' => 'Our Mission',
                'content' => 'We work alongside communities to deliver education, care, and relief with dignity.',
                'route_name' => 'frontend.platform',
                'meta_title' => $siteName . ' | Our Mission',
                'meta_description' => 'Learn how our mission centers on education, health, relief, and local partnership.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Our Mission',
                    'og_description' => 'Learn how our mission centers on education, health, relief, and local partnership.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.platform'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Our Mission',
                    'url' => route('frontend.platform'),
                    'description' => 'We work alongside communities to deliver education, care, and relief with dignity.',
                ],
                'sort_order' => 2,
                'page_content' => $this->localizedContent([
                    'profile_title' => 'Our Mission',
                    'profile_tagline' => 'We work alongside communities to deliver education, care, and relief with dignity.',
                    'platform_slider_images' => [],
                    'platform_image' => '',
                    'features_title' => 'What we do',
                    'features_subtitle' => 'We invest in practical programs that address real needs, not short-term optics.',
                    'features' => [
                        ['title' => 'Education support for children and youth.', 'color' => 'blue', 'icon' => 'icon_3'],
                        ['title' => 'Health outreach and family care.', 'color' => 'green', 'icon' => 'icon_11'],
                        ['title' => 'Emergency relief when crisis hits.', 'color' => 'red', 'icon' => 'icon_5'],
                        ['title' => 'Community partnerships with local leaders.', 'color' => 'purple', 'icon' => 'icon_7'],
                        ['title' => 'Transparent reporting for donors.', 'color' => 'blue', 'icon' => 'icon_2'],
                        ['title' => 'Volunteer coordination and training.', 'color' => 'green', 'icon' => 'icon_8'],
                    ],
                    'choose_title' => 'Why support our work?',
                    'choose_col_1_text' => 'Supported by local partners',
                    'choose_col_1_image' => '',
                    'choose_col_2_text' => 'Community focus',
                    'choose_col_2_value' => '24/7',
                    'choose_col_3_text' => 'Program areas',
                    'choose_col_3_value' => '3+',
                ]),
            ],
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'content' => 'We are a non-profit team focused on education, care, and relief.',
                'route_name' => 'frontend.about-us',
                'meta_title' => $siteName . ' | About Us',
                'meta_description' => 'Discover who we are, how we work, and why our approach is community-led.',
                'og_tags' => [
                    'og_title' => $siteName . ' | About Us',
                    'og_description' => 'Discover who we are, how we work, and why our approach is community-led.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.about-us'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'About Us',
                    'url' => route('frontend.about-us'),
                    'description' => 'We are a non-profit team focused on education, care, and relief.',
                ],
                'sort_order' => 3,
                'page_content' => $this->localizedContent([
                    'results_subtitle' => 'Community impact',
                    'results_title' => 'How do we deliver meaningful results?',
                    'results_description' => 'We build practical, transparent programs that focus on long-term support for people and communities.',
                    'different_subtitle' => 'Why we are different',
                    'different_title' => 'We work with people, not for them.',
                    'different_description' => 'Our approach is collaborative, local, and rooted in dignity. We listen first and act with care.',
                    'different_check' => 'Community-led support',
                    'different_image' => '',
                    'promise_subtitle' => 'Our promise',
                    'promise_title' => 'We stay accountable to every family and donor.',
                    'promise_description' => 'We keep our work simple, transparent, and focused on the real needs that matter most.',
                    'promise_check' => 'Transparent reporting',
                    'promise_image' => '',
                    'solutions_subtitle' => 'Our approach',
                    'solutions_title' => 'Programs designed for lasting change',
                    'solutions_description' => 'Each initiative is shaped to respond to community needs with practical support and local partnership.',
                    'solution_cards' => [
                        ['title' => 'Education support', 'description' => 'Scholarships, school supplies, and learning support for children and youth.', 'icon' => 'sol_1'],
                        ['title' => 'Health outreach', 'description' => 'Health education, basic care, and referrals that make support easier to access.', 'icon' => 'sol_2'],
                        ['title' => 'Emergency relief', 'description' => 'Rapid help for families facing crisis, displacement, or urgent hardship.', 'icon' => 'sol_3'],
                    ],
                    'interests_title' => 'Where support matters most',
                    'interest_cards' => [
                        ['title' => 'Meals and essentials', 'description' => 'Helping families access the things they need most, when they need them most.', 'icon' => 'int_1'],
                        ['title' => 'Youth mentoring', 'description' => 'Guidance, encouragement, and opportunities for young people to grow.', 'icon' => 'int_2'],
                        ['title' => 'Reports and transparency', 'description' => 'Clear reporting so supporters can see how the work is making a difference.', 'icon' => 'int_3'],
                        ['title' => 'Community partnerships', 'description' => 'Working side by side with local organizations to make support stronger.', 'icon' => 'int_4'],
                        ['title' => 'Volunteer care', 'description' => 'Equipping volunteers with simple, effective ways to help.', 'icon' => 'int_5'],
                        ['title' => 'Ongoing support', 'description' => 'Stay connected through regular updates, needs, and opportunities to serve.', 'icon' => 'int_6'],
                    ],
                    'ready_title' => 'Ready to help build stronger communities?',
                ]),
            ],
            [
                'slug' => 'product',
                'title' => 'Our Programs',
                'content' => 'Our programs are designed to support communities through education, health, relief, and long-term empowerment.',
                'route_name' => 'frontend.product',
                'meta_title' => $siteName . ' | Our Programs',
                'meta_description' => 'Explore the programs and community support initiatives we currently offer.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Our Programs',
                    'og_description' => 'Explore the programs and community support initiatives we currently offer.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.product'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'CollectionPage',
                    'name' => 'Our Programs',
                    'url' => route('frontend.product'),
                    'description' => 'Our programs are designed to support communities through education, health, relief, and long-term empowerment.',
                ],
                'sort_order' => 4,
                'page_content' => $this->localizedContent([
                    'description' => 'Our programs are designed to support communities through education, health, relief, and long-term empowerment.',
                    'products_title' => 'Our Programs',
                    'partners_title' => 'Our supporters',
                    'partner_images' => [],
                ]),
            ],
            [
                'slug' => 'history',
                'title' => 'History',
                'content' => 'Our history is rooted in local service, long-term partnerships, and practical support for families.',
                'route_name' => 'frontend.history',
                'meta_title' => $siteName . ' | History',
                'meta_description' => 'Read how our organization grew through community-led service and shared action.',
                'og_tags' => [
                    'og_title' => $siteName . ' | History',
                    'og_description' => 'Read how our organization grew through community-led service and shared action.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.history'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'History',
                    'url' => route('frontend.history'),
                    'description' => 'Our history is rooted in local service, long-term partnerships, and practical support for families.',
                ],
                'sort_order' => 6,
            ],
            [
                'slug' => 'jobs-announcement',
                'title' => 'Jobs Announcement',
                'content' => 'Open opportunities and future roles will be shared here as the organization grows.',
                'route_name' => 'frontend.page',
                'meta_title' => $siteName . ' | Jobs Announcement',
                'meta_description' => 'View current and upcoming opportunities to join our team.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Jobs Announcement',
                    'og_description' => 'View current and upcoming opportunities to join our team.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.page', ['slug' => 'jobs-announcement']),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Jobs Announcement',
                    'url' => route('frontend.page', ['slug' => 'jobs-announcement']),
                    'description' => 'Open opportunities and future roles will be shared here as the organization grows.',
                ],
                'sort_order' => 7,
            ],
            [
                'slug' => 'annual-report',
                'title' => 'Annual Report',
                'content' => 'Annual reports, program summaries, and financial highlights will be published here.',
                'route_name' => 'frontend.page',
                'meta_title' => $siteName . ' | Annual Report',
                'meta_description' => 'Review our yearly reports and impact summaries.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Annual Report',
                    'og_description' => 'Review our yearly reports and impact summaries.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.page', ['slug' => 'annual-report']),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Annual Report',
                    'url' => route('frontend.page', ['slug' => 'annual-report']),
                    'description' => 'Annual reports, program summaries, and financial highlights will be published here.',
                ],
                'sort_order' => 8,
            ],
            [
                'slug' => 'strategic-plan',
                'title' => 'Strategic Plan',
                'content' => 'Our strategic plan outlines the priorities, partnerships, and goals guiding our work.',
                'route_name' => 'frontend.page',
                'meta_title' => $siteName . ' | Strategic Plan',
                'meta_description' => 'See the long-term goals that shape our community impact.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Strategic Plan',
                    'og_description' => 'See the long-term goals that shape our community impact.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.page', ['slug' => 'strategic-plan']),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Strategic Plan',
                    'url' => route('frontend.page', ['slug' => 'strategic-plan']),
                    'description' => 'Our strategic plan outlines the priorities, partnerships, and goals guiding our work.',
                ],
                'sort_order' => 9,
            ],
            [
                'slug' => 'partner',
                'title' => 'Partner',
                'content' => 'We welcome partners who want to support community-led education, health, and relief programs.',
                'route_name' => 'frontend.page',
                'meta_title' => $siteName . ' | Partner',
                'meta_description' => 'Learn how to partner with us for greater community impact.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Partner',
                    'og_description' => 'Learn how to partner with us for greater community impact.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.page', ['slug' => 'partner']),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Partner',
                    'url' => route('frontend.page', ['slug' => 'partner']),
                    'description' => 'We welcome partners who want to support community-led education, health, and relief programs.',
                ],
                'sort_order' => 10,
            ],
            [
                'slug' => 'volunteer',
                'title' => 'Volunteer',
                'content' => 'Volunteers help us deliver practical care, organize support, and stay close to the community.',
                'route_name' => 'frontend.page',
                'meta_title' => $siteName . ' | Volunteer',
                'meta_description' => 'Find out how to volunteer with our team and serve the community.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Volunteer',
                    'og_description' => 'Find out how to volunteer with our team and serve the community.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.page', ['slug' => 'volunteer']),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Volunteer',
                    'url' => route('frontend.page', ['slug' => 'volunteer']),
                    'description' => 'Volunteers help us deliver practical care, organize support, and stay close to the community.',
                ],
                'sort_order' => 11,
            ],
            [
                'slug' => 'image-gallery',
                'title' => 'Image Gallery',
                'content' => 'Browse the public image archive for photos, logos, and visual assets from the website.',
                'route_name' => 'frontend.gallery',
                'meta_title' => $siteName . ' | Image Gallery',
                'meta_description' => 'Browse the public image archive for photos, logos, and visual assets from the website.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Image Gallery',
                    'og_description' => 'Browse the public image archive for photos, logos, and visual assets from the website.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.gallery'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'CollectionPage',
                    'name' => 'Image Gallery',
                    'url' => route('frontend.gallery'),
                    'description' => 'Browse the public image archive for photos, logos, and visual assets from the website.',
                ],
                'sort_order' => 12,
                'page_content' => $this->localizedContent([
                    'hero_badge' => 'Public archive',
                    'hero_title' => 'Image Gallery',
                    'hero_subtitle' => 'Visual assets from the website',
                    'hero_description' => 'Browse the public image archive for photos, logos, and visual assets stored under public/images.',
                    'gallery_intro' => 'The gallery pulls directly from the live public/images directory so new files appear here automatically.',
                ]),
            ],
            [
                'slug' => 'video',
                'title' => 'Video Stories',
                'content' => 'Videos that share updates, testimony, and community stories will appear here.',
                'route_name' => 'frontend.page',
                'meta_title' => $siteName . ' | Video Stories',
                'meta_description' => 'Watch videos that highlight our programs and community impact.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Video Stories',
                    'og_description' => 'Watch videos that highlight our programs and community impact.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.page', ['slug' => 'video']),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Video Stories',
                    'url' => route('frontend.page', ['slug' => 'video']),
                    'description' => 'Videos that share updates, testimony, and community stories will appear here.',
                ],
                'sort_order' => 13,
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact',
                'content' => 'Get in touch for partnerships, volunteering, and support.',
                'route_name' => 'frontend.contact',
                'meta_title' => $siteName . ' | Contact',
                'meta_description' => 'Reach our team for questions, partnerships, and support.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Contact',
                    'og_description' => 'Reach our team for questions, partnerships, and support.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.contact'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Contact',
                    'url' => route('frontend.contact'),
                    'description' => 'Get in touch for partnerships, volunteering, and support.',
                ],
                'sort_order' => 14,
                'page_content' => $this->localizedContent([
                    'page_title' => 'Contact Us',
                    'page_intro' => 'We are here to help. Feel free to reach out through any of the channels below or send us a message directly.',
                    'contact_info_title' => 'Contact Information',
                    'confirm_open' => 'Open this contact method?',
                    'address' => '123 Main Street, Phnom Penh, Cambodia',
                    'phone' => '+855 23 123 456',
                    'email' => 'info@bandoskomar.org',
                    'office_hours' => 'Monday-Friday, 8:00-17:00 (ICT)',
                    'form_title' => 'Send Us a Message',
                    'form_subtitle' => 'Fill out the form below and we will get back to you as soon as possible.',
                    'success_message' => 'Thank you! Your message has been sent and we will respond shortly.',
                    'target_email' => 'info@bandoskomar.org',
                    'labels' => [
                        'full_name' => 'Full Name',
                        'email_address' => 'Email Address',
                        'message' => 'Message',
                        'send_message' => 'Send Message',
                    ],
                    'placeholders' => [
                        'full_name' => 'Your full name',
                        'email_address' => 'you@example.com',
                        'message' => 'How can we help you?',
                    ],
                    'messages' => [
                        'no_methods' => 'No contact methods are available yet.',
                    ],
                    'contact_methods' => [
                        [
                            'key' => 'email',
                            'label' => 'Email',
                            'value' => 'info@bandoskomar.org',
                            'url' => 'mailto:info@bandoskomar.org',
                            'enabled' => true,
                        ],
                        [
                            'key' => 'whatsapp',
                            'label' => 'WhatsApp',
                            'value' => '+85512345678',
                            'url' => 'https://wa.me/85512345678',
                            'enabled' => true,
                        ],
                        [
                            'key' => 'telegram',
                            'label' => 'Telegram',
                            'value' => '@89engine_support',
                            'url' => 'https://t.me/89engine_support',
                            'enabled' => true,
                        ],
                        [
                            'key' => 'signal',
                            'label' => 'Signal',
                            'value' => '+85512345678',
                            'url' => 'https://signal.me/#p/+85512345678',
                            'enabled' => true,
                        ],
                        [
                            'key' => 'teams',
                            'label' => 'Microsoft Teams',
                            'value' => 'support@89engine.com',
                            'url' => 'https://teams.microsoft.com/l/chat/0/0?users=support%4089engine.com',
                            'enabled' => true,
                        ],
                        [
                            'key' => 'wechat',
                            'label' => 'WeChat',
                            'value' => '89engine_support',
                            'url' => 'weixin://dl/chat?89engine_support',
                            'enabled' => false,
                        ],
                    ],
                ]),
            ],
            [
                'slug' => 'donate',
                'title' => 'Donate',
                'content' => 'Support our programs and help us reach more families and communities.',
                'route_name' => 'frontend.donate',
                'meta_title' => $siteName . ' | Donate',
                'meta_description' => 'Support our programs with a donation and help expand our impact.',
                'og_tags' => [
                    'og_title' => $siteName . ' | Donate',
                    'og_description' => 'Support our programs with a donation and help expand our impact.',
                    'og_image' => $siteLogoUrl,
                    'og_type' => 'website',
                ],
                'canonical_url' => route('frontend.donate'),
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => 'Donate',
                    'url' => route('frontend.donate'),
                    'description' => 'Support our programs and help us reach more families and communities.',
                ],
                'sort_order' => 15,
            ],
        ];

        foreach ($pages as $pageData) {
            $pageData['menu_group'] = $pageData['menu_group'] ?? $this->menuGroupForSlug($pageData['slug']);

            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                array_merge(
                    $pageData,
                    [
                        'is_active' => true,
                        'sitemap_include' => true,
                        'translations' => $this->localizedTranslations($pageData['title'], $pageData['content']),
                    ]
                )
            );
        }

        Page::where('slug', 'image')->update([
            'is_active' => false,
            'menu_group' => 'hidden',
            'sitemap_include' => false,
        ]);

        $this->command?->info('Website pages seeded successfully.');
    }

    private function localizedTranslations(string $title, string $content): array
    {
        $translations = [];

        foreach (PageLocales::all() as $locale) {
            $translations[$locale] = [
                'title' => $title,
                'content' => $content,
            ];
        }

        return $translations;
    }

    private function localizedContent(array $content): array
    {
        $localized = [];

        foreach (PageLocales::all() as $locale) {
            $localized[$locale] = $content;
        }

        return $localized;
    }

    private function menuGroupForSlug(string $slug): string
    {
        return match ($slug) {
            'home', 'platform', 'about-us', 'product', 'history' => 'main',
            'jobs-announcement', 'annual-report', 'strategic-plan', 'partner' => 'resources',
            'volunteer', 'image-gallery', 'video' => 'involved',
            'image' => 'hidden',
            'contact', 'donate' => 'hidden',
            default => 'more',
        };
    }
}
