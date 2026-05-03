@extends('frontend.layouts.app')

@section('content')
<section class="relative h-[80vh] min-h-[600px] flex items-center overflow-hidden" style="--hero-bk-navy: #1E2D53; --hero-bk-orange: #F68B1E;">
        <!-- Hero Image Background -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1497486751825-1233686d5d80?q=80&w=1600&auto=format&fit=crop" alt="Hero Background" class="w-full h-full object-cover">
            <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(30,45,83,0.9) 0%, rgba(30,45,83,0.5) 60%, transparent 100%);"></div>
        </div>

        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 animate-bounce" style="background: rgba(246,139,30,0.2); border: 1px solid rgba(246,139,30,0.3); color: #F68B1E;">
                    {{ $page->content['hero']['badge'] ?? 'Impact since 1989' }}
                </div>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-[1.1] mb-6 tracking-tight" style="color: #ffffff;">
                    {{ $page->content['hero']['title'] ?? 'Empowering Communities' }} <br>
                    <span style="color: #F68B1E;">{{ $page->content['hero']['subtitle'] ?? 'for a Better Future' }}</span>
                </h1>
                <p class="text-lg md:text-xl mb-10 max-w-2xl leading-relaxed" style="color: rgba(255,255,255,0.85);">
                    {{ $page->content['hero']['description'] ?? 'Bandos Komar is a local NGO dedicated to improving education in Cambodia...' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('frontend.product') }}" class="px-10 py-4 rounded-full font-extrabold text-lg shadow-lg transition-all text-center" style="background: #F68B1E; color: #ffffff;" onmouseover="this.style.background='#e07a1a';this.style.transform='scale(1.05)'" onmouseout="this.style.background='#F68B1E';this.style.transform='scale(1)'">Our Programs</a>
                    <a href="{{ route('frontend.about-us') }}" class="px-10 py-4 rounded-full font-extrabold text-lg border-2 transition-all text-center" style="background: transparent; color: #F68B1E; border-color: #F68B1E;" onmouseover="this.style.background='#F68B1E';this.style.color='#ffffff'" onmouseout="this.style.background='transparent';this.style.color='#F68B1E'">Learn More</a>
                </div>
            </div>
        </div>
    </section>


    <section id="openings" class="border-y border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-700">Current openings</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Available positions</h2>
            </div>
            <div class="mt-10">
                @if(isset($page->content['jobs']) && is_array($page->content['jobs']) && count($page->content['jobs']))
                    @foreach($page->content['jobs'] as $job)
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 mb-6 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-slate-900">{{ $job['title'] ?? 'Job Title' }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $job['location'] ?? 'Phnom Penh / Remote' }} • {{ $job['type'] ?? 'Full-time' }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $job['summary'] ?? 'Brief description of the role and responsibilities.' }}</p>
                                </div>
                                <div class="flex gap-3">
                                    <a href="{{ $job['apply_link'] ?? '#' }}" class="rounded-full bg-orange-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-700">Apply Now</a>
                                    <a href="#" class="rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700">Details</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @else
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 text-slate-600 text-center">
                        No positions are currently open. Please check back later or email careers@bandoskomar.org for general inquiries.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Why join us?</h2>
                <p class="mt-4 text-base leading-7 text-slate-600">We offer a mission-driven environment focused on education, integrity, and community impact.</p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-slate-900">Mission-driven</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Your work directly supports education programs for children and communities in need.</p>
                </article>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-slate-900">Collaborative culture</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Partnerships with communities, volunteers, and donors to create sustainable outcomes.</p>
                </article>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-slate-900">Growth & development</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Ongoing training and professional development as we scale our impact.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
