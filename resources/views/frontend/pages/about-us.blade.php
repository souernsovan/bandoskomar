    @extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();
        $toAssetUrl = function (?string $path, string $fallback = '') {
            $path = is_string($path) ? trim($path) : '';

            if ($path === '') {
                return $fallback;
            }

            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//'])) {
                return $path;
            }

            return asset($path);
        };

        $missionTitle = $c['different_title'] ?? 'A community-first NGO with clear accountability';
        $missionDescription = $c['different_description'] ?? 'We exist to strengthen education, family wellbeing, and emergency response through local partnerships and steady follow-through.';
        $missionImage = $c['different_image'] ?? '';
        $aboutStripImages = array_values(array_filter(is_array($c['about_strip_images'] ?? null) ? $c['about_strip_images'] : []));

        $missionPrinciples = [
            [
                'title' => $c['mission_1_title'] ?? 'Mission',
                'description' => $c['mission_1_description'] ?? 'Support children and families through education, health, and relief.',
            ],
            [
                'title' => $c['mission_2_title'] ?? 'Vision',
                'description' => $c['mission_2_description'] ?? 'Communities where people can learn, grow, and thrive with dignity.',
            ],
            [
                'title' => $c['mission_3_title'] ?? 'Values',
                'description' => $c['mission_3_description'] ?? 'Transparency, stewardship, dignity, and collaboration guide every decision.',
            ],
        ];

        $approachTitle = $c['solutions_title'] ?? 'Programs designed for lasting change';
        $approachDescription = $c['solutions_description'] ?? 'Each initiative is shaped to respond to community needs with practical support and local partnership.';
        $solutionCards = $c['solution_cards'] ?? [
            ['title' => 'Education support', 'description' => 'Scholarships, school supplies, and learning support for children and youth.', 'image' => ''],
            ['title' => 'Health outreach', 'description' => 'Health education, basic care, and referrals that make support easier to access.', 'image' => ''],
            ['title' => 'Emergency relief', 'description' => 'Rapid help for families facing crisis, displacement, or urgent hardship.', 'image' => ''],
        ];

        $valuesTitle = $c['interests_title'] ?? 'Where support matters most';
        $interestCards = $c['interest_cards'] ?? [
            ['title' => 'Meals and essentials', 'description' => 'Helping families access the things they need most, when they need them most.', 'image' => ''],
            ['title' => 'Youth mentoring', 'description' => 'Guidance, encouragement, and opportunities for young people to grow.', 'image' => ''],
            ['title' => 'Reports and transparency', 'description' => 'Clear reporting so supporters can see how the work is making a difference.', 'image' => ''],
            ['title' => 'Community partnerships', 'description' => 'Working side by side with local organizations to make support stronger.', 'image' => ''],
            ['title' => 'Volunteer care', 'description' => 'Equipping volunteers with simple, effective ways to help.', 'image' => ''],
            ['title' => 'Ongoing support', 'description' => 'Stay connected through regular updates, needs, and opportunities to serve.', 'image' => ''],
        ];

        $ctaTitle = $c['ready_title'] ?? 'Support the work, share the story, or ask how to get involved.';
        $ctaDescription = $c['ready_description'] ?? 'About Us is stronger when donors, volunteers, and partners move together with the communities we serve.';
    @endphp

    <div class="bg-white">
        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">
                <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5 lg:p-10">
                        <p class="text-[0.7rem] font-black uppercase tracking-[0.35em] text-[#F68B1E]">Mission, vision, values</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-[#1E2D53] sm:text-4xl">
                            {{ $missionTitle }}
                        </h2>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                            {{ $missionDescription }}
                        </p>
                        
                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            @foreach ($missionPrinciples as $principle)
                                <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5">
                                    <div class="inline-flex rounded-full bg-[#1E2D53]/10 px-3 py-1 text-[0.62rem] font-black uppercase tracking-[0.35em] text-[#1E2D53]">
                                        {{ $principle['title'] }}
                                    </div>
                                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $principle['description'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -top-6 right-8 h-24 w-24 rounded-full bg-[#F68B1E]/10"></div>
                        @if (!empty($missionImage))
                            <img src="{{ $toAssetUrl($missionImage) }}" alt="{{ $missionTitle }}" class="relative z-10 w-full rounded-[2rem] object-cover shadow-2xl shadow-slate-900/10">
                        @else
                            <div class="relative z-10 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl shadow-slate-900/10">
                                <div class="bg-[#1E2D53] px-6 py-5 text-white">
                                    <p class="text-[0.65rem] font-black uppercase tracking-[0.35em] text-[#F68B1E]">Our promise</p>
                                    <p class="mt-3 text-xl font-black leading-snug sm:text-2xl">
                                        We keep support visible, accountable, and close to the people it serves.
                                    </p>
                                </div>
                                <div class="grid gap-4 p-6 sm:grid-cols-2">
                                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                                        <p class="text-sm font-black text-[#1E2D53]">Listen first</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">Local voices help shape every response.</p>
                                    </div>
                                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                                        <p class="text-sm font-black text-[#1E2D53]">Act clearly</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">Simple action is easier to understand and trust.</p>
                                    </div>
                                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                                        <p class="text-sm font-black text-[#1E2D53]">Report honestly</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">We show what happened and what changed.</p>
                                    </div>
                                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                                        <p class="text-sm font-black text-[#1E2D53]">Stay present</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">Our work continues beyond one-off campaigns.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if (!empty($aboutStripImages))
            <section class="bg-white">
                <div class="w-full py-8 lg:py-12">
                    <div class="overflow-hidden border-y border-slate-200 bg-white">
                        <div class="grid grid-cols-2 gap-0 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-5">
                        @foreach (array_slice($aboutStripImages, 0, 5) as $stripImage)
                            <div class="overflow-hidden border-b border-r border-slate-200 bg-slate-100">
                                <img src="{{ $toAssetUrl($stripImage) }}" alt="About page image {{ $loop->iteration }}" class="h-52 w-full object-cover sm:h-60 lg:h-72 xl:h-80" loading="lazy">
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">
                <div class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#F68B1E]">{{ $valuesTitle }}</p>
                    <h2 class="mt-4 text-3xl font-black tracking-tight text-[#1E2D53] sm:text-4xl lg:text-5xl">
                        Programs shaped for practical impact
                    </h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        {{ $approachDescription }}
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($solutionCards as $card)
                        @php $cardImage = $card['image'] ?? ''; @endphp
                        <article class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-slate-50 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="aspect-[16/11] bg-slate-100">
                                @if (!empty($cardImage))
                                    <img src="{{ $toAssetUrl($cardImage) }}" alt="{{ $card['title'] ?? '' }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-[#1E2D53]/8 to-[#F68B1E]/12">
                                        <span class="rounded-full bg-white px-4 py-2 text-xs font-black uppercase tracking-[0.35em] text-[#1E2D53]">Program</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-black text-[#1E2D53]">{{ $card['title'] ?? '' }}</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['description'] ?? '' }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">
                <div class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-[#F68B1E]">{{ $valuesTitle }}</p>
                    <h2 class="mt-4 text-3xl font-black tracking-tight text-[#1E2D53] sm:text-4xl lg:text-5xl">
                        Where support matters most
                    </h2>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($interestCards as $card)
                        @php $cardImage = $card['image'] ?? ''; @endphp
                        <article class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="aspect-[16/10] bg-slate-100">
                                @if (!empty($cardImage))
                                    <img src="{{ $toAssetUrl($cardImage) }}" alt="{{ $card['title'] ?? '' }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-[#1E2D53]/8 to-[#F68B1E]/12">
                                        <div class="rounded-full bg-white px-4 py-2 text-xs font-black uppercase tracking-[0.35em] text-[#1E2D53]">
                                            NGO
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                            <h3 class="mt-5 text-lg font-black text-[#1E2D53]">{{ $card['title'] ?? '' }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['description'] ?? '' }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#1E2D53] text-white">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">
                <div class="rounded-[2.5rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-slate-900/20 lg:p-10">
                    <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                        <div>
                            <p class="text-[0.7rem] font-black uppercase tracking-[0.35em] text-[#F68B1E]">Next step</p>
                            <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                                {{ $ctaTitle }}
                            </h2>
                            <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-200 sm:text-base">
                                {{ $ctaDescription }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-4 lg:justify-end lg:items-start">
                            <a href="{{ route('frontend.product') }}" class="rounded-full bg-[#F68B1E] px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-900/20 transition hover:bg-[#e27c14]">
                                View programs
                            </a>
                            <a href="{{ route('frontend.contact') }}" class="rounded-full border border-white/25 px-7 py-3.5 text-sm font-bold text-white transition hover:bg-white/10">
                                Contact us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
