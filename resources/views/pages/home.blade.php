@extends('layouts.app')

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
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="relative h-[80vh] min-h-[600px] flex items-center overflow-hidden">
    <!-- Hero Image Background -->
    <div class="absolute inset-0 z-0">
        <img src="{{ $page->content['hero']['image'] ?? '/assets/images/hero.png' }}" alt="Hero Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-bk-navy/90 via-bk-navy/50 to-transparent"></div>
    </div>

    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-block px-4 py-1.5 bg-bk-orange/20 border border-bk-orange/30 rounded-full text-bk-orange font-bold text-xs uppercase tracking-widest mb-6 animate-bounce">
                {{ $page->content['hero']['badge'] ?? 'Impact since 1989' }}
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white leading-[1.1] mb-6 tracking-tight">
                {{ $page->content['hero']['title'] ?? 'Empowering Communities' }} <br>
                <span class="text-bk-orange">{{ $page->content['hero']['subtitle'] ?? 'for a Better Future' }}</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl leading-relaxed">
                {{ $page->content['hero']['description'] ?? 'Bandos Komar is a local NGO dedicated to improving education in Cambodia...' }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('programs') }}" class="bg-bk-orange text-white px-10 py-4 rounded-full font-extrabold text-lg shadow-lg hover:scale-105 hover:shadow-bk-orange/40 transition-all text-center">Our Programs</a>
                <a href="{{ route('about') }}" class="bg-transparent text-white px-10 py-4 rounded-full font-extrabold text-lg border-2 border-white hover:bg-white hover:text-bk-navy transition-all text-center">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-bk-orange font-extrabold uppercase tracking-widest text-sm mb-4 block">{{ $page->content['stats']['heading'] ?? 'Who We Are' }}</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-bk-navy mb-6 leading-tight">{{ $page->content['stats']['title'] ?? 'Bandos Komar Association' }}</h2>
                <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                    {{ $page->content['stats']['description'] ?? '' }}
                </p>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-3 text-bk-navy font-extrabold text-lg group">
                    Read Our Story 
                    <span class="w-10 h-10 rounded-full bg-bk-navy/5 flex items-center justify-center group-hover:bg-bk-navy group-hover:text-white transition-all">
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </span>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-6">
                @if(isset($page->content['stats']['items']))
                    @foreach($page->content['stats']['items'] as $item)
                    <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 hover:shadow-2xl hover:shadow-bk-navy/5 transition-all text-center group">
                        <span class="block text-4xl font-black text-bk-orange mb-2 group-hover:scale-110 transition-transform">{{ $item['value'] ?? '' }}</span>
                        <span class="text-gray-500 font-bold uppercase text-xs tracking-widest">{{ $item['label'] ?? '' }}</span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Community Promise Section -->
<section class="py-24 bg-gray-50 overflow-hidden">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="home-reveal">
                <span class="text-bk-orange font-extrabold uppercase tracking-widest text-sm mb-4 block">Community First</span>
                <h2 class="text-3xl md:text-5xl font-black text-bk-navy mb-6 leading-tight">Practical support shaped by local families, teachers, and children.</h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-8">
                    Our work begins by listening. We partner with communities to understand the daily barriers children face, then build education, health, and family support programs that can last beyond a single project cycle.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <i data-lucide="school" class="w-8 h-8 text-bk-orange mb-4"></i>
                        <h3 class="font-black text-bk-navy mb-2">Schools</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Better learning spaces and stronger classroom support.</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <i data-lucide="home" class="w-8 h-8 text-bk-orange mb-4"></i>
                        <h3 class="font-black text-bk-navy mb-2">Families</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Parent engagement and care around each child.</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <i data-lucide="sprout" class="w-8 h-8 text-bk-orange mb-4"></i>
                        <h3 class="font-black text-bk-navy mb-2">Future</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Skills and confidence that continue to grow.</p>
                    </div>
                </div>
            </div>

            <div class="relative home-reveal">
                <div class="absolute -left-8 -top-8 w-28 h-28 bg-bk-orange/10 rounded-full soft-float"></div>
                <img src="https://images.unsplash.com/photo-1509099836639-18ba1795216d?q=80&w=1200&auto=format&fit=crop" alt="Children learning together" class="relative z-10 w-full h-[520px] object-cover rounded-[3rem] shadow-2xl">
                <div class="absolute z-20 -bottom-8 left-8 right-8 bg-white rounded-3xl p-6 shadow-2xl border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-bk-navy text-white flex items-center justify-center">
                            <i data-lucide="heart-handshake" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-bk-orange uppercase tracking-widest mb-1">Our promise</p>
                            <p class="text-bk-navy font-extrabold leading-snug">Every action should help children learn, feel safe, and stay connected to opportunity.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section class="py-24 bg-gray-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-bk-orange font-extrabold uppercase tracking-widest text-sm mb-4 block">What We Do</span>
            <h2 class="text-3xl md:text-5xl font-extrabold text-bk-navy mb-6 tracking-tight">Our Integrated Programs</h2>
            <div class="w-20 h-1.5 bg-bk-orange mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @if(isset($page->content['programs']))
                @foreach($page->content['programs'] as $program)
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all duration-500 border border-transparent hover:border-bk-navy/5 group relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-32 h-32 bg-bk-navy opacity-[0.02] rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="w-16 h-16 bg-bk-navy/5 rounded-2xl flex items-center justify-center text-bk-navy mb-8 group-hover:bg-bk-navy group-hover:text-white transition-all duration-300">
                        <i data-lucide="{{ $program['icon'] ?? 'book-open' }}" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-bk-navy mb-4">{{ $program['title'] ?? '' }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-8">{{ $program['description'] ?? '' }}</p>
                    <a href="{{ route('programs') }}" class="inline-flex items-center gap-2 text-bk-orange font-bold text-sm uppercase tracking-wider group/link">
                        Learn More <i data-lucide="chevron-right" class="w-4 h-4 group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- How We Work Section -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center max-w-3xl mx-auto mb-16 home-reveal">
            <span class="text-bk-orange font-extrabold uppercase tracking-widest text-sm mb-4 block">How We Work</span>
            <h2 class="text-3xl md:text-5xl font-black text-bk-navy mb-6">A simple model for lasting impact</h2>
            <p class="text-gray-600 text-lg leading-relaxed">We combine field knowledge, community trust, and careful follow-up so support reaches the children and families who need it most.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="home-reveal bg-gray-50 rounded-[2rem] p-8 border border-gray-100 hover:-translate-y-2 hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-bk-orange text-white flex items-center justify-center font-black text-xl mb-8">1</div>
                <h3 class="text-2xl font-black text-bk-navy mb-4">Listen locally</h3>
                <p class="text-gray-500 leading-relaxed">Community voices guide priorities, from classroom needs to child protection and family wellbeing.</p>
            </div>
            <div class="home-reveal bg-gray-50 rounded-[2rem] p-8 border border-gray-100 hover:-translate-y-2 hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-bk-orange text-white flex items-center justify-center font-black text-xl mb-8">2</div>
                <h3 class="text-2xl font-black text-bk-navy mb-4">Build together</h3>
                <p class="text-gray-500 leading-relaxed">Teachers, parents, local leaders, and staff work side by side to make support practical.</p>
            </div>
            <div class="home-reveal bg-gray-50 rounded-[2rem] p-8 border border-gray-100 hover:-translate-y-2 hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-bk-orange text-white flex items-center justify-center font-black text-xl mb-8">3</div>
                <h3 class="text-2xl font-black text-bk-navy mb-4">Follow through</h3>
                <p class="text-gray-500 leading-relaxed">We track progress, adapt programs, and keep the focus on real outcomes for children.</p>
            </div>
        </div>
    </div>
</section>

<!-- Impact Story Section -->
<section class="py-24 bg-bk-navy relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <img src="https://images.unsplash.com/photo-1497486751825-1233686d5d80?q=80&w=1600&auto=format&fit=crop" alt="Classroom background" class="w-full h-full object-cover">
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="home-reveal">
                <span class="text-bk-orange font-extrabold uppercase tracking-widest text-sm mb-4 block">Field Story</span>
                <h2 class="text-3xl md:text-5xl font-black text-white mb-6 leading-tight">Small changes can keep a child in school.</h2>
                <p class="text-gray-300 text-lg leading-relaxed mb-8">
                    A safe classroom, a trained teacher, clean water, and a family that feels supported can change the direction of a child’s day. Bandos Komar focuses on these practical pieces because they add up to opportunity.
                </p>
                <a href="{{ route('resources.photo-gallery') }}" class="inline-flex items-center gap-3 bg-white text-bk-navy px-8 py-4 rounded-full font-black hover:bg-bk-orange hover:text-white transition-all">
                    See Our Work <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>

            <div class="home-reveal grid grid-cols-2 gap-5">
                <div class="bg-white/10 border border-white/10 rounded-3xl p-6 backdrop-blur-sm">
                    <i data-lucide="book-open-check" class="w-9 h-9 text-bk-orange mb-8"></i>
                    <h3 class="text-3xl font-black text-white mb-2">Learning</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">Helping children stay engaged in school.</p>
                </div>
                <div class="bg-white/10 border border-white/10 rounded-3xl p-6 backdrop-blur-sm mt-10">
                    <i data-lucide="droplets" class="w-9 h-9 text-bk-orange mb-8"></i>
                    <h3 class="text-3xl font-black text-white mb-2">Health</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">Supporting safe hygiene and wellbeing.</p>
                </div>
                <div class="bg-white/10 border border-white/10 rounded-3xl p-6 backdrop-blur-sm">
                    <i data-lucide="users-round" class="w-9 h-9 text-bk-orange mb-8"></i>
                    <h3 class="text-3xl font-black text-white mb-2">Care</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">Working with families and communities.</p>
                </div>
                <div class="bg-white/10 border border-white/10 rounded-3xl p-6 backdrop-blur-sm mt-10">
                    <i data-lucide="shield-check" class="w-9 h-9 text-bk-orange mb-8"></i>
                    <h3 class="text-3xl font-black text-white mb-2">Safety</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">Keeping children protected and included.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="bg-bk-navy rounded-[3rem] p-12 md:p-24 text-center relative overflow-hidden group">
            <!-- Animated Circles -->
            <div class="absolute -top-32 -right-32 w-80 h-80 bg-bk-orange/10 rounded-full transition-transform duration-1000 group-hover:scale-150"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/5 rounded-full transition-transform duration-1000 group-hover:scale-150"></div>

            <div class="relative z-10 max-w-3xl mx-auto">
                <h2 class="text-4xl md:text-6xl font-black text-white mb-8 leading-tight tracking-tight">{{ $page->content['cta']['title'] ?? 'Support Our Mission' }}</h2>
                <p class="text-gray-300 text-xl mb-12 leading-relaxed">
                    {{ $page->content['cta']['description'] ?? '' }}
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-6">
                    <a href="{{ route('donate') }}" class="bg-bk-orange text-white px-12 py-5 rounded-full font-black text-xl shadow-2xl hover:scale-105 hover:shadow-bk-orange/50 transition-all">Donate Now</a>
                    <a href="{{ route('get-involved.support-us') }}" class="bg-transparent text-white px-12 py-5 rounded-full font-black text-xl border-2 border-white/30 hover:border-white transition-all">Become a Volunteer</a>
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
