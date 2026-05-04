@php
    $enrichmentPage = $page ?? null;
    $enrichmentSlug = $enrichmentPage ? ($enrichmentPage->slug ?? '') : '';
    $enrichmentTitle = $enrichmentPage && method_exists($enrichmentPage, 'getTitleForLocale') ? $enrichmentPage->getTitleForLocale() : 'Community update';

    $resolveLink = function ($action): ?string {
        if (!is_array($action) || empty($action)) {
            return null;
        }

        if (!empty($action['href'])) {
            return $action['href'];
        }

        if (!empty($action['route']) && \Illuminate\Support\Facades\Route::has($action['route'])) {
            return route($action['route'], $action['params'] ?? []);
        }

        return null;
    };

    $sharedStory = [
        'eyebrow' => 'How we create impact',
        'title' => 'Practical support that lasts beyond a single donation',
        'description' => 'Bandos Komar works with local leaders, families, and volunteers to make support visible, accountable, and useful over time.',
        'cards' => [
            [
                'step' => '01',
                'title' => 'Listen locally',
                'description' => 'Community voices shape the priorities, programs, and follow-up support we provide.',
            ],
            [
                'step' => '02',
                'title' => 'Deliver with dignity',
                'description' => 'We keep the work practical, respectful, and focused on immediate needs as well as long-term outcomes.',
            ],
            [
                'step' => '03',
                'title' => 'Measure results',
                'description' => 'Transparent reporting helps supporters see where resources go and what changes because of the work.',
            ],
        ],
    ];

    $pageStories = [
        'about-us' => [
            'eyebrow' => 'Mission, vision, values',
            'title' => 'A community-first NGO with clear accountability',
            'description' => 'We exist to strengthen education, family wellbeing, and emergency response through local partnerships and steady follow-through.',
            'cards' => [
                ['step' => '01', 'title' => 'Mission', 'description' => 'Support children and families through education, health, and relief.'],
                ['step' => '02', 'title' => 'Vision', 'description' => 'Communities where people can learn, grow, and thrive with dignity.'],
                ['step' => '03', 'title' => 'Values', 'description' => 'Transparency, stewardship, dignity, and collaboration guide every decision.'],
            ],
            'primary' => ['label' => 'View programs', 'route' => 'frontend.product'],
            'secondary' => ['label' => 'Contact us', 'route' => 'frontend.contact'],
        ],
        'platform' => [
            'eyebrow' => 'Our model',
            'title' => 'A simple process for lasting community impact',
            'description' => 'We design programs around local needs, then follow through with training, support, and accountability.',
            'cards' => [
                ['step' => '01', 'title' => 'Education', 'description' => 'Learning support, materials, and environments that help children stay in school.'],
                ['step' => '02', 'title' => 'Health & care', 'description' => 'Wellness support, outreach, and family guidance that keeps households stable.'],
                ['step' => '03', 'title' => 'Emergency relief', 'description' => 'Fast, practical help when communities face crisis or disruption.'],
            ],
            'primary' => ['label' => 'Learn about us', 'route' => 'frontend.about-us'],
            'secondary' => ['label' => 'Support programs', 'route' => 'frontend.donate'],
        ],
        'product' => [
            'eyebrow' => 'Program areas',
            'title' => 'Support focused on real needs and measurable outcomes',
            'description' => 'Our initiatives are built to help communities now while strengthening local capacity for the future.',
            'cards' => [
                ['step' => '01', 'title' => 'Education support', 'description' => 'Scholarships, supplies, mentoring, and stronger learning environments.'],
                ['step' => '02', 'title' => 'Family support', 'description' => 'Basic essentials, encouragement, and practical help for households.'],
                ['step' => '03', 'title' => 'Transparent reporting', 'description' => 'Clear updates so donors and partners can see the difference their help makes.'],
            ],
            'primary' => ['label' => 'Donate now', 'route' => 'frontend.donate'],
            'secondary' => ['label' => 'Partner with us', 'route' => 'frontend.contact'],
        ],
        'contact' => [
            'eyebrow' => 'Get connected',
            'title' => 'Other ways to reach the team',
            'description' => 'Choose the channel that fits your need. We aim to respond quickly and keep communication respectful.',
            'cards' => [
                ['step' => '01', 'title' => 'General inquiries', 'description' => 'Questions about programs, updates, or the organization.'],
                ['step' => '02', 'title' => 'Partnerships', 'description' => 'Schools, donors, and organizations interested in working together.'],
                ['step' => '03', 'title' => 'Volunteering', 'description' => 'Skill-based or field support for events, content, and community care.'],
            ],
            'primary' => ['label' => 'Donate', 'route' => 'frontend.donate'],
            'secondary' => ['label' => 'Volunteer', 'route' => 'frontend.page', 'params' => ['slug' => 'volunteer']],
        ],
        'donate' => [
            'eyebrow' => 'Where gifts go',
            'title' => 'Your support becomes practical help for families and children',
            'description' => 'Every donation helps us deliver food, school support, health outreach, and emergency relief with clear stewardship.',
            'cards' => [
                ['step' => '01', 'title' => 'One-time gifts', 'description' => 'Immediate support for the most urgent needs.'],
                ['step' => '02', 'title' => 'Monthly giving', 'description' => 'Reliable support that helps us plan and stay responsive.'],
                ['step' => '03', 'title' => 'In-kind help', 'description' => 'Supplies, equipment, and practical donations that reduce operating costs.'],
            ],
            'primary' => ['label' => 'Give today', 'href' => 'mailto:donate@bandoskomar.org'],
            'secondary' => ['label' => 'Learn more', 'route' => 'frontend.about-us'],
        ],
        'history' => [
            'eyebrow' => 'Our journey',
            'title' => 'Milestones that shaped our mission',
            'description' => 'From a small local effort to a broader network of support, our story has always been rooted in community partnership.',
            'cards' => [
                ['step' => '1989', 'title' => 'Founded', 'description' => 'The organization began with volunteers committed to education and local care.'],
                ['step' => '2005', 'title' => 'Expanded programs', 'description' => 'Health outreach and family support became stronger parts of the mission.'],
                ['step' => '2024+', 'title' => 'Looking ahead', 'description' => 'We continue to grow partnerships that make support more sustainable.'],
            ],
            'primary' => ['label' => 'Read about us', 'route' => 'frontend.about-us'],
            'secondary' => ['label' => 'Support the work', 'route' => 'frontend.donate'],
        ],
        'partner' => [
            'eyebrow' => 'Partnerships',
            'title' => 'Ways to collaborate with us',
            'description' => 'We welcome schools, foundations, companies, churches, and community groups that want to create measurable impact.',
            'cards' => [
                ['step' => '01', 'title' => 'Funding', 'description' => 'Support a program, a campaign, or a specific community need.'],
                ['step' => '02', 'title' => 'In-kind support', 'description' => 'Donate supplies, equipment, services, or professional expertise.'],
                ['step' => '03', 'title' => 'Shared outreach', 'description' => 'Co-host events, awareness campaigns, or volunteer activations.'],
            ],
            'primary' => ['label' => 'Contact us', 'route' => 'frontend.contact'],
            'secondary' => ['label' => 'See programs', 'route' => 'frontend.product'],
        ],
        'volunteer' => [
            'eyebrow' => 'Volunteer with us',
            'title' => 'Flexible roles for local and remote volunteers',
            'description' => 'You can contribute time, expertise, and care in ways that fit your schedule and strengths.',
            'cards' => [
                ['step' => '01', 'title' => 'Field support', 'description' => 'Help at events, schools, and community activities when needed.'],
                ['step' => '02', 'title' => 'Skills-based', 'description' => 'Offer translation, design, teaching, IT, or administration support.'],
                ['step' => '03', 'title' => 'Remote help', 'description' => 'Support content, outreach, research, and coordination from anywhere.'],
            ],
            'primary' => ['label' => 'Register interest', 'route' => 'frontend.contact'],
            'secondary' => ['label' => 'Partner with us', 'route' => 'frontend.page', 'params' => ['slug' => 'partner']],
        ],
        'annual-report' => [
            'eyebrow' => 'Annual reporting',
            'title' => 'What the numbers say, and what we learned',
            'description' => 'We publish reporting to stay accountable and to help supporters understand how programs are changing lives.',
            'cards' => [
                ['step' => '01', 'title' => 'Program results', 'description' => 'Reach, participation, and impact across the year.'],
                ['step' => '02', 'title' => 'Financial clarity', 'description' => 'A simple view of where funds were allocated.'],
                ['step' => '03', 'title' => 'Next priorities', 'description' => 'What we plan to improve and expand in the next cycle.'],
            ],
            'primary' => ['label' => 'View reports', 'href' => route('frontend.page', ['slug' => 'annual-report']) . '#downloads'],
            'secondary' => ['label' => 'Support programs', 'route' => 'frontend.donate'],
        ],
        'strategic-plan' => [
            'eyebrow' => 'Strategic priorities',
            'title' => 'A focused plan for sustainable growth',
            'description' => 'Our priorities help us stay grounded in access, quality, and long-term community ownership.',
            'cards' => [
                ['step' => '01', 'title' => 'Access', 'description' => 'Reach more families and underserved communities.'],
                ['step' => '02', 'title' => 'Quality', 'description' => 'Strengthen program design, training, and follow-up.'],
                ['step' => '03', 'title' => 'Sustainability', 'description' => 'Build local capacity so impact can continue for years.'],
            ],
            'primary' => ['label' => 'Read our story', 'route' => 'frontend.about-us'],
            'secondary' => ['label' => 'See programs', 'route' => 'frontend.product'],
        ],
        'jobs-announcement' => [
            'eyebrow' => 'Working with us',
            'title' => 'Mission-first roles with room to grow',
            'description' => 'We value people who bring care, initiative, and collaboration to community-focused work.',
            'cards' => [
                ['step' => '01', 'title' => 'Purpose-led', 'description' => 'Your work supports real people and practical outcomes.'],
                ['step' => '02', 'title' => 'Collaborative', 'description' => 'We value clear communication and teamwork across the organization.'],
                ['step' => '03', 'title' => 'Learning culture', 'description' => 'You will grow through feedback, training, and real-world problem solving.'],
            ],
            'primary' => ['label' => 'Contact us', 'route' => 'frontend.contact'],
            'secondary' => ['label' => 'Volunteer first', 'route' => 'frontend.page', 'params' => ['slug' => 'volunteer']],
        ],
        'image-gallery' => [
            'eyebrow' => 'Photo stories',
            'title' => 'Images that show the work in action',
            'description' => 'These photos help tell the story of the communities, partnerships, and moments behind the mission.',
            'cards' => [
                ['step' => '01', 'title' => 'Classrooms', 'description' => 'Learning spaces, students, and teachers working together.'],
                ['step' => '02', 'title' => 'Community care', 'description' => 'Program deliveries, events, and local support in action.'],
                ['step' => '03', 'title' => 'Everyday impact', 'description' => 'The small moments that show progress, dignity, and hope.'],
            ],
            'primary' => ['label' => 'Watch stories', 'route' => 'frontend.page', 'params' => ['slug' => 'video-stories']],
            'secondary' => ['label' => 'Contact us', 'route' => 'frontend.contact'],
        ],
        'video-stories' => [
            'eyebrow' => 'Video stories',
            'title' => 'Short films from the communities we serve',
            'description' => 'Watch testimonies, field updates, and behind-the-scenes stories about how the work comes together.',
            'cards' => [
                ['step' => '01', 'title' => 'Voices from the field', 'description' => 'Hear directly from people involved in the programs.'],
                ['step' => '02', 'title' => 'Program updates', 'description' => 'See how support turns into practical outcomes.'],
                ['step' => '03', 'title' => 'Share with others', 'description' => 'Help spread the word by sharing stories with your network.'],
            ],
            'primary' => ['label' => 'View gallery', 'route' => 'frontend.page', 'params' => ['slug' => 'image-gallery']],
            'secondary' => ['label' => 'Contact us', 'route' => 'frontend.contact'],
        ],
        'default' => [
            'eyebrow' => 'More to explore',
            'title' => 'This page is still growing',
            'description' => 'While this content is being expanded, you can still explore the programs, stories, and ways to support the mission.',
            'cards' => [
                ['step' => '01', 'title' => 'Programs', 'description' => 'See the practical work happening across education, care, and relief.'],
                ['step' => '02', 'title' => 'Contact', 'description' => 'Reach the team for questions, partnerships, or support.'],
                ['step' => '03', 'title' => 'Donate', 'description' => 'Help fund the next round of community support.'],
            ],
            'primary' => ['label' => 'View programs', 'route' => 'frontend.product'],
            'secondary' => ['label' => 'Contact us', 'route' => 'frontend.contact'],
        ],
    ];

    $pageBlock = $pageStories[$enrichmentSlug] ?? $pageStories['default'];
    $sharedPrimary = $pageBlock['primary'] ?? ['label' => 'Get involved', 'route' => 'frontend.contact'];
    $sharedSecondary = $pageBlock['secondary'] ?? ['label' => 'Learn more', 'route' => 'frontend.about-us'];
    $sharedPrimaryHref = $resolveLink($sharedPrimary);
    $sharedSecondaryHref = $resolveLink($sharedSecondary);
@endphp

<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $sharedStory['eyebrow'] }}</p>
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                {{ $sharedStory['title'] }}
            </h2>
            <p class="mt-4 text-base leading-7 text-slate-600">
                {{ $sharedStory['description'] }}
            </p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($sharedStory['cards'] as $card)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <span class="inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.3em] text-teal-700">
                        {{ $card['step'] }}
                    </span>
                    <h3 class="mt-5 text-xl font-semibold text-slate-900">{{ $card['title'] }}</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-xl shadow-teal-900/5">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-700">{{ $pageBlock['eyebrow'] }}</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    {{ $pageBlock['title'] }}
                </h2>
                <p class="mt-4 text-base leading-7 text-slate-600">
                    {{ $pageBlock['description'] }}
                </p>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($pageBlock['cards'] as $card)
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <span class="inline-flex rounded-full bg-orange-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.3em] text-orange-700">
                            {{ $card['step'] }}
                        </span>
                        <h3 class="mt-5 text-xl font-semibold text-slate-900">{{ $card['title'] }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-teal-700 text-white">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-100">Next step</p>
            <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                Support the work, share the story, or ask how to get involved.
            </h2>
            <p class="mt-4 text-sm leading-6 text-teal-50">
                {{ $enrichmentTitle }} is stronger when donors, volunteers, and partners move together with the communities we serve.
            </p>
        </div>

        <div class="mt-8 flex flex-wrap gap-4">
            @if ($sharedPrimaryHref)
                <a href="{{ $sharedPrimaryHref }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-teal-800 transition hover:bg-teal-50">
                    {{ $sharedPrimary['label'] ?? 'Get involved' }}
                </a>
            @endif

            @if ($sharedSecondaryHref)
                <a href="{{ $sharedSecondaryHref }}" class="rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                    {{ $sharedSecondary['label'] ?? 'Learn more' }}
                </a>
            @endif
        </div>
    </div>
</section>
