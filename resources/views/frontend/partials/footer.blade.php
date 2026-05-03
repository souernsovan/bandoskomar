@php
    $footerPages = collect($headerPages ?? []);
    $footerMorePages = $footerPages->filter(
        fn ($page) => in_array($page->getMenuGroup(), ['resources', 'involved', 'more'], true)
    );
@endphp

<footer class="bg-[#1B2B4B] text-white pt-20 pb-8 mt-20 relative overflow-hidden">
    {{-- Decorative Circle --}}
    <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#F5A623] opacity-10 rounded-full"></div>

    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

            {{-- Brand --}}
            <div>
                <h2 class="text-2xl font-extrabold mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 bg-white rounded flex items-center justify-center text-[#1B2B4B] text-sm font-black">BK</div>
                    Bandos Komar
                </h2>
                <p class="text-gray-400 text-sm leading-relaxed mb-8 max-w-xs">
                    Dedicated to improving education in Cambodia, especially in rural areas, providing basic support
                    and progressively developing projects focused on education.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#F5A623] transition-colors">
                        <i data-lucide="facebook" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#F5A623] transition-colors">
                        <i data-lucide="twitter" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#F5A623] transition-colors">
                        <i data-lucide="instagram" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#F5A623] transition-colors">
                        <i data-lucide="youtube" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-lg font-bold mb-2 text-white">Quick Links</h4>
                <div class="w-10 h-1 bg-[#F5A623] mb-6"></div>
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('frontend.home') }}" class="text-gray-400 hover:text-[#F5A623] transition-all flex items-center gap-2 text-sm">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.about-us') }}" class="text-gray-400 hover:text-[#F5A623] transition-all flex items-center gap-2 text-sm">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> About Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.history') }}" class="text-gray-400 hover:text-[#F5A623] transition-all flex items-center gap-2 text-sm">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Our History
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.product') }}" class="text-gray-400 hover:text-[#F5A623] transition-all flex items-center gap-2 text-sm">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Programs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.contact') }}" class="text-gray-400 hover:text-[#F5A623] transition-all flex items-center gap-2 text-sm">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i> Contact
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Resources --}}
            <div>
                <h4 class="text-lg font-bold mb-2 text-white">Resources</h4>
                <div class="w-10 h-1 bg-[#F5A623] mb-6"></div>
                <ul class="space-y-4">
                    
                    @foreach ($footerMorePages as $footerPage)
                        <li>
                            <a href="{{ $footerPage->url }}" class="text-gray-400 hover:text-[#F5A623] transition-all flex items-center gap-2 text-sm">
                                <i data-lucide="chevron-right" class="w-3 h-3"></i> {{ $footerPage->getTitleForLocale() }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact Us --}}
            <div>
                <h4 class="text-lg font-bold mb-2 text-white">Contact Us</h4>
                <div class="w-10 h-1 bg-[#F5A623] mb-6"></div>
                <ul class="space-y-5">
                    <li class="flex gap-4 text-sm text-gray-400 leading-relaxed">
                        <i data-lucide="map-pin" class="w-5 h-5 text-[#F5A623] shrink-0 mt-0.5"></i>
                        <span>#12, Street 315, Boeung Kak II, Tuol Kork, Phnom Penh, Cambodia</span>
                    </li>
                    <li class="flex gap-4 text-sm text-gray-400 leading-relaxed">
                        <i data-lucide="phone" class="w-5 h-5 text-[#F5A623] shrink-0"></i>
                        <span>+855 (0) 23 881 234 / 881 235</span>
                    </li>
                    <li class="flex gap-4 text-sm text-gray-400 leading-relaxed">
                        <i data-lucide="mail" class="w-5 h-5 text-[#F5A623] shrink-0"></i>
                        <span>info@bandoskomar.org</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Bottom Bar --}}
        <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500">
            <p>&copy; {{ date('Y') }} Bandos Komar Association. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="{{ route('frontend.contact') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="{{ route('frontend.contact') }}" class="hover:text-white transition-colors">Terms of Use</a>
                <a href="{{ route('frontend.home') }}" class="hover:text-white transition-colors">Sitemap</a>
            </div>
        </div>
    </div>
</footer>