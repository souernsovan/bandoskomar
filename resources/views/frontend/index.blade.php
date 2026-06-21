@extends('frontend.layouts.app')

@section('styles')
<style>
    .home-reveal {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .home-reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    .soft-float {
        animation: softFloat 6s ease-in-out infinite;
    }
    @keyframes softFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }
    .ngo-section {
        position: relative;
        overflow: hidden;
    }
    .ngo-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top left, rgba(246, 139, 30, 0.07), transparent 34%),
            radial-gradient(circle at bottom right, rgba(30, 45, 83, 0.05), transparent 28%);
        pointer-events: none;
    }
    .ngo-card {
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
    }
    .ngo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.10);
        border-color: rgba(246, 139, 30, 0.18);
    }
</style>
@endsection

@section('content')
@php
    $home = is_array($pageContent ?? null) ? $pageContent : [];
    $toAssetUrl = function (?string $path, string $fallback = '') {
        $path = is_string($path) ? trim($path) : '';

        if ($path === '') {
            return $fallback;
        }

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        return asset($path);
    };

    $programStyles = is_array($styles ?? null) ? $styles : [];
    $supporterImages = is_array($partnerImages ?? null) ? $partnerImages : [];
@endphp
<!-- Hero Section -->
 <section class="relative h-[80vh] min-h-[600px] flex items-center overflow-hidden" style="--hero-bk-navy: #1E2D53; --hero-bk-orange: #F68B1E;">
        <!-- Hero Image Background -->
        <div class="absolute inset-0 z-0">
            <img src="{{ $toAssetUrl($heroImage, 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?q=80&w=1600&auto=format&fit=crop') }}" alt="Hero Background" class="w-full h-full object-cover">
            <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(30,45,83,0.9) 0%, rgba(30,45,83,0.5) 60%, transparent 100%);"></div>
        </div>

        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 animate-bounce" style="background: rgba(246,139,30,0.2); border: 1px solid rgba(246,139,30,0.3); color: #F68B1E;">
                    {{ $home['hero_badge'] ?? 'Impact since 1989' }}
                </div>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-[1.1] mb-6 tracking-tight" style="color: #ffffff;">
                    {{ $heroHeadline }}
                </h1>
                <p class="text-lg md:text-xl mb-10 max-w-2xl leading-relaxed" style="color: rgba(255,255,255,0.85);">
                    {{ $heroDescription }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('frontend.product') }}" class="px-10 py-4 rounded-full font-extrabold text-lg shadow-lg transition-all text-center" style="background: #F68B1E; color: #ffffff;" onmouseover="this.style.background='#e07a1a';this.style.transform='scale(1.05)'" onmouseout="this.style.background='#F68B1E';this.style.transform='scale(1)'">Our Programs</a>
                    <a href="{{ route('frontend.about-us') }}" class="px-10 py-4 rounded-full font-extrabold text-lg border-2 transition-all text-center" style="background: transparent; color: #F68B1E; border-color: #F68B1E;" onmouseover="this.style.background='#F68B1E';this.style.color='#ffffff'" onmouseout="this.style.background='transparent';this.style.color='#F68B1E'">Learn More</a>
                </div>
            </div>
        </div>
    </section>

<!-- Community Promise Section -->
<section class="py-24 bg-gray-50 overflow-hidden">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="home-reveal">
                <span class="text-[#F68B1E] font-extrabold uppercase tracking-widest text-sm mb-4 block">{{ $marketingTitle }}</span>
                <h2 class="text-3xl md:text-5xl font-black text-[#1E2D53] mb-6 leading-tight">{{ $companyTitle }}</h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-8">
                    {{ $marketingDescription }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <i data-lucide="school" class="w-8 h-8 text-[#F68B1E] mb-4"></i>
                        <h3 class="font-black text-[#1E2D53] mb-2">{{ $valueProp1['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $valueProp1['desc'] }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <i data-lucide="home" class="w-8 h-8 text-[#F68B1E] mb-4"></i>
                        <h3 class="font-black text-[#1E2D53] mb-2">{{ $valueProp2['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $valueProp2['desc'] }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <i data-lucide="sprout" class="w-8 h-8 text-[#F68B1E] mb-4"></i>
                        <h3 class="font-black text-[#1E2D53] mb-2">{{ $valueProp3['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $valueProp3['desc'] }}</p>
                    </div>
                </div>
            </div>

            <div class="relative home-reveal">
                <div class="absolute -left-8 -top-8 w-28 h-28 bg-[#F68B1E]/10 rounded-full soft-float"></div>
                <img src="{{ $toAssetUrl($capabilitiesImage ?: $marketingImage, 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?q=80&w=1200&auto=format&fit=crop') }}" alt="Children learning together" class="relative z-10 w-full h-[520px] object-cover rounded-[3rem] shadow-2xl">
                <div class="absolute z-20 -bottom-8 left-8 right-8 bg-white rounded-3xl p-6 shadow-2xl border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-[#1E2D53] text-white flex items-center justify-center">
                            <i data-lucide="heart-handshake" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-[#F68B1E] uppercase tracking-widest mb-1">{{ $companyTitle }}</p>
                            <p class="text-[#1E2D53] font-extrabold leading-snug">{{ $companyDescription }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section class="py-24 bg-white ngo-section">
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="max-w-3xl mx-auto text-center mb-14">
            <span class="inline-flex items-center rounded-full bg-[#F68B1E]/10 px-4 py-1.5 text-xs font-black uppercase tracking-[0.3em] text-[#F68B1E]">
                {{ $colorChoiceTitle }}
            </span>
            <h2 class="mt-5 text-3xl md:text-5xl font-black tracking-tight text-[#1E2D53]">
                {{ $styleTitle }}
            </h2>
            <p class="mt-5 text-lg leading-relaxed text-slate-600">
                Community-centered programs, presented clearly and simply so people can understand the impact at a glance.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @forelse($programStyles as $styleIndex => $style)
                @php
                    $cardTitle = trim((string) ($style['title'] ?? '')) !== '' ? $style['title'] : $styleTitle;
                    $cardDescription = trim((string) ($style['description'] ?? '')) !== '' ? $style['description'] : $colorChoiceTitle;
                @endphp
                <div class="ngo-card group overflow-hidden rounded-[1.5rem] bg-white border border-slate-100 flex flex-col">
                    <div class="relative h-36 overflow-hidden">
                        @if(!empty($style['image']))
                            <img src="{{ $toAssetUrl($style['image']) }}" alt="{{ $cardTitle }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="h-full w-full bg-gradient-to-br from-[#1E2D53]/10 via-white to-[#F68B1E]/10"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1E2D53]/20 via-transparent to-transparent"></div>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-3">
                            <span class="inline-flex rounded-full bg-[#1E2D53]/5 px-3 py-1 text-[10px] font-black uppercase tracking-[0.25em] text-[#1E2D53]/65">
                                {{ $colorChoiceTitle }}
                            </span>
                        </div>
                        <h3 class="text-lg md:text-xl font-black leading-tight text-[#1E2D53]">{{ $cardTitle }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600 max-h-20 overflow-hidden">{{ $cardDescription }}</p>

                        @if(!empty($style['colors']) && is_array($style['colors']))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($style['colors'] as $color)
                                    @php $hex = is_array($color) ? ($color['hex'] ?? '#6366f1') : '#6366f1'; @endphp
                                    <span class="inline-flex items-center gap-2 rounded-full bg-[#1E2D53]/5 px-2.5 py-1.5 text-[11px] font-bold text-[#1E2D53]">
                                        <span class="h-3 w-3 rounded-full border border-white/80" style="background: {{ $hex }}"></span>
                                        {{ is_array($color) ? ($color['name'] ?? 'Color') : 'Color' }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-5">
                            <a href="{{ route('frontend.product') }}" class="inline-flex items-center gap-2 text-sm font-black uppercase tracking-wider text-[#F68B1E]">
                                Learn More <i data-lucide="chevron-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-gray-300 bg-slate-50 p-8 text-gray-600 md:col-span-2 lg:col-span-4 text-center">
                    Programs will appear here once configured in the admin panel.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Trust Section -->
<section class="py-24 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <div class="home-reveal">
                <span class="text-[#F68B1E] font-extrabold uppercase tracking-widest text-sm mb-4 block">{{ $trustSubtitle }}</span>
                <h2 class="text-3xl md:text-5xl font-black text-[#1E2D53] mb-6 leading-tight">
                    {{ $trustTitle }}
                </h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-8">
                    {{ $trustDescription }}
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-[#F68B1E]/10 text-[#F68B1E] flex items-center justify-center font-black">1</div>
                        <div>
                            <h3 class="font-black text-[#1E2D53] mb-1">{{ $trustStep1['title'] }}</h3>
                            <p class="text-slate-600 leading-relaxed">{{ $trustStep1['desc'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-[#1E2D53]/10 text-[#1E2D53] flex items-center justify-center font-black">2</div>
                        <div>
                            <h3 class="font-black text-[#1E2D53] mb-1">{{ $trustStep2['title'] }}</h3>
                            <p class="text-slate-600 leading-relaxed">{{ $trustStep2['desc'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-black">3</div>
                        <div>
                            <h3 class="font-black text-[#1E2D53] mb-1">{{ $trustStep3['title'] }}</h3>
                            <p class="text-slate-600 leading-relaxed">{{ $trustStep3['desc'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="home-reveal relative">
                <div class="absolute -top-8 -left-8 w-28 h-28 bg-[#F68B1E]/10 rounded-full soft-float"></div>
                <img src="{{ $toAssetUrl($trustImage ?: $capabilitiesImage ?: $heroImage, 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?q=80&w=1200&auto=format&fit=crop') }}" alt="Community support" class="relative z-10 w-full h-[520px] object-cover rounded-[2.5rem] shadow-2xl">
                <div class="absolute z-20 -bottom-8 left-8 right-8 bg-white rounded-3xl p-6 shadow-2xl border border-gray-100">
                    <p class="text-xs font-black text-[#F68B1E] uppercase tracking-widest mb-2">{{ $trustQuoteLabel }}</p>
                    <p class="text-[#1E2D53] font-extrabold leading-snug">
                        {{ $trustQuoteText }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Story Section -->
<section class="py-16 lg:py-20 bg-[#1E2D53] relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <img src="{{ $toAssetUrl($mobileBg, 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?q=80&w=1600&auto=format&fit=crop') }}" alt="Classroom background" class="w-full h-full object-cover">
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1.06fr_0.94fr] lg:items-start">
            <div class="home-reveal">
                <span class="text-[#F68B1E] font-extrabold uppercase tracking-widest text-sm mb-4 block">Field Story</span>
                <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">{{ $mobileTitle }}</h2>
                <p class="text-gray-300 text-lg leading-relaxed mb-8">
                    {{ $marketingDescription }}
                </p>
                <a href="{{ route('frontend.about-us') }}" class="inline-flex items-center gap-3 bg-white text-[#1E2D53] px-8 py-4 rounded-full font-black hover:bg-[#F68B1E] hover:text-white transition-all">
                    See Our Work <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([$impactFeature1, $impactFeature2, $impactFeature3] as $index => $feature)
                        <div class="rounded-2xl border border-white/10 bg-white/10 backdrop-blur-xl p-4 text-left shadow-lg {{ $index === 2 ? 'md:col-span-2' : '' }}">
                            <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-[#1E2D53] font-black text-sm">
                                {{ $index + 1 }}
                            </div>
                            <p class="text-xs font-black uppercase tracking-[0.25em] text-[#F68B1E] mb-2">{{ $impactFeatureLabel }}</p>
                            <h3 class="text-base md:text-lg font-black text-white leading-tight">{{ $feature['title'] }}</h3>
                            <p class="mt-2 text-sm leading-5 text-slate-200">{{ $feature['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="home-reveal relative lg:justify-self-end">
                <div class="absolute -top-8 -left-8 w-28 h-28 bg-[#F68B1E]/10 rounded-full soft-float"></div>
                @if(!empty($mobileImage))
                    <img src="{{ $toAssetUrl($mobileImage) }}" alt="{{ $mobileTitle }}" class="relative z-10 w-full max-w-2xl max-h-[440px] rounded-3xl shadow-xl object-cover">
                @endif
                <div class="absolute z-20 -bottom-6 left-6 right-6 bg-white rounded-3xl p-5 shadow-2xl border border-gray-100">
                    <p class="text-xs font-black text-[#F68B1E] uppercase tracking-widest mb-2">{{ $impactFeatureLabel }}</p>
                    <p class="text-[#1E2D53] font-extrabold leading-snug">
                        {{ $trustQuoteText }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend.partials.supporters-strip', [
    'supporterLogos' => $supporterImages,
    'supportersTitle' => $supporterTitle ?? 'Our supporters',
    'supportersDescription' => 'A growing network of organizations and friends helping us serve communities with care.',
    'supportersEmptyText' => 'Supporter logos will appear here once uploaded in the admin panel.',
])

<!-- CTA Section -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="bg-[#1E2D53] rounded-[3rem] p-12 md:p-24 text-center relative overflow-hidden group">
            <div class="absolute -top-32 -right-32 w-80 h-80 bg-[#F68B1E]/10 rounded-full transition-transform duration-1000 group-hover:scale-150"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/5 rounded-full transition-transform duration-1000 group-hover:scale-150"></div>

            <div class="relative z-10 max-w-3xl mx-auto">
                <h2 class="text-4xl md:text-6xl font-black text-white mb-8 leading-tight tracking-tight">{{ $marketingTitle }}</h2>
                <p class="text-gray-300 text-xl mb-12 leading-relaxed">{{ $marketingDescription }}</p>
                <div class="flex flex-col sm:flex-row justify-center gap-6">
                    <a href="{{ route('frontend.donate') }}" class="bg-[#F68B1E] text-white px-12 py-5 rounded-full font-black text-xl shadow-2xl hover:scale-105 hover:shadow-[#F68B1E]/50 transition-all">Donate Now</a>
                    <a href="{{ route('frontend.page', ['slug' => 'volunteer']) }}" class="bg-transparent text-white px-12 py-5 rounded-full font-black text-xl border-2 border-white/30 hover:border-white transition-all">Become a Volunteer</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const revealItems = document.querySelectorAll('.home-reveal');
        if (!('IntersectionObserver' in window)) {
            revealItems.forEach((item) => item.classList.add('is-visible'));
            return;
        }
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        revealItems.forEach((item) => observer.observe(item));
    });
</script>
@endsection
