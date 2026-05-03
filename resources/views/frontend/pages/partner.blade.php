@extends('frontend.layouts.app')
@section('content')
<section class="relative h-[80vh] min-h-[600px] flex items-center overflow-hidden" style="--hero-bk-navy: #1E2D53; --hero-bk-orange: #F68B1E;">
        <!-- Hero Image Background -->
        <div class="absolute inset-0 z-0">
            <img src="http://127.0.0.1:8001/assets/images/hero.png" alt="Hero Background" class="w-full h-full object-cover">
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

<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">How you can partner</h2>
            <p class="mt-4 text-base leading-7 text-slate-600">We work with schools, donors, corporations, and NGOs to support education programs.</p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Funding & Grants</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Support programs, materials, teacher stipends, and infrastructure projects.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">In-kind & Expertise</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Offer training, curriculum resources, monitoring & evaluation, or tech support.</p>
            </article>
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('frontend.contact') }}" class="rounded-full bg-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-700">Contact us to discuss a partnership</a>
        </div>
    </div>
</section>
@endsection
