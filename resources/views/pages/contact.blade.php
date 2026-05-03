@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <span class="text-bk-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ $page->content['header']['badge'] ?? 'Contact Us' }}</span>
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight leading-tight">
            {{ $page->content['header']['title'] ?? 'We Are Always Here to Help' }}
        </h1>
        <p class="text-gray-400 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
            {{ $page->content['header']['description'] ?? 'Reach out to us through the form below or visit our office. We look forward to connecting with you.' }}
        </p>
    </div>
</section>

@include('pages.partials.admin-body')

<!-- Contact Section (Form and Address in 2 Columns) -->
<section class="py-24 bg-white relative z-10">
    <div class="container mx-auto px-4 md:px-6">
        <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Column 1: Contact Form -->
                <div class="p-10 md:p-16 border-b lg:border-b-0 lg:border-r border-gray-100">
                    <h2 class="text-3xl font-black text-bk-navy mb-8 tracking-tight">Send a Message</h2>
                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Full Name</label>
                                <input type="text" name="name" placeholder="Your Name" class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-bk-orange focus:ring-4 focus:ring-bk-orange/10 outline-none transition-all font-semibold">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Email</label>
                                <input type="email" name="email" placeholder="Your Email" class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-bk-orange focus:ring-4 focus:ring-bk-orange/10 outline-none transition-all font-semibold">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Subject</label>
                            <input type="text" name="subject" placeholder="What is this about?" class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-bk-orange focus:ring-4 focus:ring-bk-orange/10 outline-none transition-all font-semibold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Message</label>
                            <textarea rows="4" name="message" placeholder="Your message..." class="w-full px-6 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-bk-orange focus:ring-4 focus:ring-bk-orange/10 outline-none transition-all font-semibold resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-bk-navy text-white px-8 py-5 rounded-2xl font-black text-lg hover:bg-bk-orange transition-all shadow-xl shadow-bk-navy/10 flex items-center justify-center gap-3 group">
                            Send Message
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>
                </div>

                <!-- Column 2: Address Information -->
                <div class="p-10 md:p-16 bg-gray-50/50">
                    <h2 class="text-3xl font-black text-bk-navy mb-10 tracking-tight">Visit Our Office</h2>
                    
                    <div class="space-y-10">
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-bk-orange shadow-sm">
                                <i data-lucide="map-pin" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Our Location</h4>
                                <p class="text-xl font-extrabold text-bk-navy leading-relaxed">
                                    {{ $page->content['info']['address'] ?? 'Phnom Penh, Cambodia' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-bk-navy shadow-sm">
                                <i data-lucide="mail" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Email Address</h4>
                                <p class="text-xl font-extrabold text-bk-navy">
                                    {{ $page->content['info']['email'] ?? 'info@bandoskomar.org' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-green-600 shadow-sm">
                                <i data-lucide="phone" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Phone Number</h4>
                                <p class="text-xl font-extrabold text-bk-navy">
                                    {{ $page->content['info']['phone'] ?? '+855 23 456 789' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Socials in Address Column -->
                    <div class="mt-16 pt-10 border-t border-gray-200">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Connect with us</h4>
                        <div class="flex gap-4">
                            <a href="{{ route('contact') }}" class="w-12 h-12 bg-bk-navy text-white rounded-xl flex items-center justify-center hover:bg-bk-orange transition-all">
                                <i data-lucide="facebook" class="w-5 h-5"></i>
                            </a>
                            <a href="{{ route('contact') }}" class="w-12 h-12 bg-bk-navy text-white rounded-xl flex items-center justify-center hover:bg-bk-orange transition-all">
                                <i data-lucide="twitter" class="w-5 h-5"></i>
                            </a>
                            <a href="{{ route('contact') }}" class="w-12 h-12 bg-bk-navy text-white rounded-xl flex items-center justify-center hover:bg-bk-orange transition-all">
                                <i data-lucide="instagram" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Big Google Map Section -->
<section class="pb-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-bk-navy tracking-tight">Find Us on the Map</h2>
            <div class="w-20 h-1.5 bg-bk-orange mx-auto mt-4 rounded-full"></div>
        </div>
        <div class="w-full h-[600px] bg-gray-100 rounded-[4rem] overflow-hidden shadow-2xl border-8 border-white group">
            @if(isset($page->content['info']['map_embed']) && $page->content['info']['map_embed'] !== '#')
                <iframe src="{{ $page->content['info']['map_embed'] }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 gap-4">
                    <i data-lucide="map" class="w-20 h-20 opacity-20"></i>
                    <p class="font-black uppercase tracking-[0.4em] text-xs">Map Location Placeholder</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
