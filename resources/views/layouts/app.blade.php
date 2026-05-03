<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bandos Komar' }} - Empowering Communities</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @yield('styles')
</head>
<body class="font-sans bg-white text-gray-900 antialiased overflow-x-hidden">
    
    <!-- Header -->
    <header class="fixed top-0 left-0 w-full h-20 bg-bk-navy z-[1000] flex items-center shadow-lg transition-all duration-300">
        <div class="container mx-auto px-4 md:px-6">
            <nav class="flex justify-between items-center w-full">
                <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline group">
                    <div class="w-11 h-11 bg-white rounded-lg flex items-center justify-center font-extrabold text-bk-navy text-xl shadow-inner transition-transform group-hover:scale-105">BK</div>
                    <span class="text-white font-extrabold text-lg md:text-xl tracking-tight uppercase">Bandos Komar</span>
                </a>

                <ul class="hidden lg:flex items-center gap-6 xl:gap-8">
                    @foreach(($frontendNavPages['main'] ?? collect()) as $navPage)
                        <li>
                            <a href="{{ $navPage['url'] }}" class="{{ $navPage['active'] ? 'text-bk-orange after:w-full' : 'text-white after:w-0' }} hover:text-bk-orange font-semibold text-[0.95rem] transition-colors py-2 relative after:content-[''] after:absolute after:bottom-0 after:left-0 after:h-0.5 after:bg-bk-orange after:transition-all hover:after:w-full">
                                {{ $navPage['title'] }}
                            </a>
                        </li>
                    @endforeach
                    
                    @if(($frontendNavPages['resources'] ?? collect())->isNotEmpty())
                    <li class="relative group">
                        <button class="flex items-center gap-1 {{ Request::routeIs('resources.*') ? 'text-bk-orange' : 'text-white' }} hover:text-bk-orange font-semibold text-[0.95rem] transition-colors py-2 focus:outline-none">
                            Info & Resources <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        <div class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-left -translate-y-2 group-hover:translate-y-0">
                            <div class="py-2">
                                @foreach($frontendNavPages['resources'] as $navPage)
                                    <a href="{{ $navPage['url'] }}" class="block px-4 py-2 text-sm {{ $navPage['active'] ? 'bg-bk-orange/10 text-bk-orange' : 'text-gray-700' }} hover:bg-bk-orange/10 hover:text-bk-orange font-semibold">{{ $navPage['title'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                    @endif

                    @if(($frontendNavPages['get-involved'] ?? collect())->isNotEmpty())
                    <li class="relative group">
                        <button class="flex items-center gap-1 {{ Request::routeIs('get-involved.*') ? 'text-bk-orange' : 'text-white' }} hover:text-bk-orange font-semibold text-[0.95rem] transition-colors py-2 focus:outline-none">
                            Get Involved <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        <div class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-left -translate-y-2 group-hover:translate-y-0">
                            <div class="py-2">
                                @foreach($frontendNavPages['get-involved'] as $navPage)
                                    <a href="{{ $navPage['url'] }}" class="block px-4 py-2 text-sm {{ $navPage['active'] ? 'bg-bk-orange/10 text-bk-orange' : 'text-gray-700' }} hover:bg-bk-orange/10 hover:text-bk-orange font-semibold">{{ $navPage['title'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                    @endif

                    @foreach(($frontendNavPages['contact'] ?? collect()) as $navPage)
                        <li>
                            <a href="{{ $navPage['url'] }}" class="{{ $navPage['active'] ? 'text-bk-orange after:w-full' : 'text-white after:w-0' }} hover:text-bk-orange font-semibold text-[0.95rem] transition-colors py-2 relative after:content-[''] after:absolute after:bottom-0 after:left-0 after:h-0.5 after:bg-bk-orange after:transition-all hover:after:w-full">
                                {{ $navPage['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="flex items-center gap-4">
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-white font-semibold text-sm bg-white/10 px-3 py-1.5 rounded-full cursor-pointer hover:bg-white/20 transition-colors focus:outline-none">
                            <i data-lucide="languages" class="w-4 h-4"></i>
                            <span>EN</span>
                        </button>
                        <div class="absolute top-full right-0 mt-2 w-32 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right -translate-y-2 group-hover:translate-y-0">
                            <div class="py-2">
                                <a href="?lang=en" class="block px-4 py-2 text-sm text-gray-700 hover:bg-bk-orange/10 hover:text-bk-orange font-semibold">English</a>
                                <a href="?lang=km" class="block px-4 py-2 text-sm text-gray-700 hover:bg-bk-orange/10 hover:text-bk-orange font-semibold">Khmer</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ $frontendDonatePage['url'] ?? route('donate') }}" class="hidden sm:block bg-bk-orange text-white px-6 py-2.5 rounded-full no-underline font-bold text-sm uppercase tracking-wider shadow-[0_4px_15px_rgba(246,139,30,0.3)] hover:scale-105 hover:shadow-[0_6px_20px_rgba(246,139,30,0.4)] transition-all">{{ $frontendDonatePage['title'] ?? 'Donate' }}</a>
                    <button class="lg:hidden text-white cursor-pointer p-1" id="menuToggle">
                        <i data-lucide="menu" class="w-8 h-8"></i>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Nav -->
    <div class="fixed top-20 -left-full w-full h-[calc(100vh-5rem)] bg-bk-navy z-[999] transition-all duration-500 ease-in-out px-8 py-10 overflow-y-auto lg:hidden" id="mobileNav">
        <ul class="flex flex-col gap-6">
            @foreach($frontendMobilePages as $navPage)
                @continue(($frontendDonatePage['slug'] ?? null) === $navPage['slug'])
                <li><a href="{{ $navPage['url'] }}" class="{{ $navPage['active'] ? 'text-bk-orange' : 'text-white' }} text-2xl font-bold no-underline flex items-center justify-between">{{ $navPage['title'] }} <i data-lucide="chevron-right" class="w-6 h-6 {{ $navPage['active'] ? 'text-bk-orange' : 'text-bk-orange/30' }}"></i></a></li>
            @endforeach
            
            <li class="mt-4"><a href="{{ $frontendDonatePage['url'] ?? route('donate') }}" class="block bg-bk-orange text-white py-4 rounded-xl text-center font-bold text-xl uppercase shadow-lg">{{ $frontendDonatePage['title'] ?? 'Donate Now' }}</a></li>
        </ul>
    </div>

    <main class="mt-20 min-h-[calc(100vh-20rem)]">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-bk-navy text-white pt-20 pb-8 mt-20 relative overflow-hidden">
        <!-- Decorative Circle -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-bk-orange opacity-10 rounded-full"></div>
        
        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <div>
                    <h2 class="text-2xl font-extrabold mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 bg-white rounded flex items-center justify-center text-bk-navy text-sm font-black">BK</div>
                        Bandos Komar
                    </h2>
                    <p class="text-gray-400 text-sm leading-relaxed mb-8 max-w-xs">
                        Dedicated to improving education in Cambodia, especially in rural areas, providing basic support and progressively developing projects focused on education.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('contact') }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bk-orange transition-colors"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                        <a href="{{ route('contact') }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bk-orange transition-colors"><i data-lucide="twitter" class="w-5 h-5"></i></a>
                        <a href="{{ route('contact') }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bk-orange transition-colors"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                        <a href="{{ route('contact') }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bk-orange transition-colors"><i data-lucide="youtube" class="w-5 h-5"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6 relative pb-3 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-1 after:bg-bk-orange">Quick Links</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Home</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> About Us</a></li>
                        <li><a href="{{ route('history') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Our History</a></li>
                        <li><a href="{{ route('programs') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Programs</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6 relative pb-3 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-1 after:bg-bk-orange">Resources</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('resources.annual-report') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Annual Reports</a></li>
                        <li><a href="{{ route('resources.publication') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Publications</a></li>
                        <li><a href="{{ route('resources.photo-gallery') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Photo Gallery</a></li>
                        <li><a href="{{ route('resources.video-center') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Video Center</a></li>
                        <li><a href="{{ route('get-involved.career') }}" class="text-gray-400 hover:text-bk-orange transition-all flex items-center gap-2 text-sm"><i data-lucide="chevron-right" class="w-3 h-3"></i> Careers</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6 relative pb-3 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-10 after:h-1 after:bg-bk-orange">Contact Us</h4>
                    <ul class="space-y-5">
                        <li class="flex gap-4 text-sm text-gray-400 leading-relaxed">
                            <i data-lucide="map-pin" class="w-5 h-5 text-bk-orange shrink-0"></i>
                            <span>#12, Street 315, Boeung Kak II, Tuol Kork, Phnom Penh, Cambodia</span>
                        </li>
                        <li class="flex gap-4 text-sm text-gray-400 leading-relaxed">
                            <i data-lucide="phone" class="w-5 h-5 text-bk-orange shrink-0"></i>
                            <span>+855 (0) 23 881 234 / 881 235</span>
                        </li>
                        <li class="flex gap-4 text-sm text-gray-400 leading-relaxed">
                            <i data-lucide="mail" class="w-5 h-5 text-bk-orange shrink-0"></i>
                            <span>info@bandoskomar.org</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500">
                <p>&copy; {{ date('Y') }} Bandos Komar Association. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="{{ route('contact') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="{{ route('contact') }}" class="hover:text-white transition-colors">Terms of Use</a>
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        const menuToggle = document.getElementById('menuToggle');
        const mobileNav = document.getElementById('mobileNav');
        const icon = menuToggle.querySelector('i');

        menuToggle.addEventListener('click', () => {
            mobileNav.classList.toggle('left-0');
            mobileNav.classList.toggle('-left-full');
            if (mobileNav.classList.contains('left-0')) {
                icon.setAttribute('data-lucide', 'x');
            } else {
                icon.setAttribute('data-lucide', 'menu');
            }
            lucide.createIcons();
        });

        // Sticky Header Effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 20) {
                header.classList.add('h-16', 'shadow-xl');
                header.classList.remove('h-20');
            } else {
                header.classList.add('h-20');
                header.classList.remove('h-16', 'shadow-xl');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
