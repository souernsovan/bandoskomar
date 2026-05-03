@extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();
        $pageTitle = $page->getTitleForLocale();

        $heroBadge = $c['hero_badge'] ?? 'Get in touch';
        $heroTitle = $c['hero_title'] ?? 'Contact Us';
        $heroSubtitle = $c['hero_subtitle'] ?? 'We would love to hear from you';
        $heroDescription = $c['hero_description'] ?? 'Whether you have questions about our programs, want to volunteer, or are interested in partnering with us, our team is here to help.';

        $contactTitle = $c['contact_title'] ?? 'Reach our team';
        $contactSubtitle = $c['contact_subtitle'] ?? 'We aim to respond within 1–2 business days';

        $address = $c['address'] ?? '123 Main Street, Phnom Penh, Cambodia';
        $phone = $c['phone'] ?? '+855 23 123 456';
        $email = $c['email'] ?? 'info@bandoskomar.org';
        $officeHours = $c['office_hours'] ?? 'Monday–Friday, 8:00–17:00 (ICT)';

        $formTitle = $c['form_title'] ?? 'Send us a message';
        $formSubtitle = $c['form_subtitle'] ?? 'Fill out the form below and we will get back to you as soon as possible.';
        $nameLabel = $c['name_label'] ?? 'Your name';
        $emailLabel = $c['email_label'] ?? 'Email address';
        $subjectLabel = $c['subject_label'] ?? 'Subject';
        $messageLabel = $c['message_label'] ?? 'Your message';
        $submitLabel = $c['submit_label'] ?? 'Send message';
        $successMessage = $c['success_message'] ?? 'Thank you! Your message has been sent and we will respond shortly.';
    @endphp

    <section class="relative h-[80vh] min-h-[600px] flex items-center overflow-hidden" style="--hero-bk-navy: #1E2D53; --hero-bk-orange: #F68B1E;">
        <!-- Hero Image Background -->
        <div class="absolute inset-0 z-0">
            <img src="http://127.0.0.1:8001/assets/images/hero.png" alt="Hero Background" class="w-full h-full object-cover">
            <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(30,45,83,0.9) 0%, rgba(30,45,83,0.5) 60%, transparent 100%);"></div>
        </div>

        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 animate-bounce" style="background: rgba(246,139,30,0.2); border: 1px solid rgba(246,139,30,0.3); color: #F68B1E;">
                    {{ $heroBadge }}
                </div>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-[1.1] mb-6 tracking-tight" style="color: #ffffff;">
                    {{ $heroTitle }} <br>
                    <span style="color: #F68B1E;">{{ $heroSubtitle }}</span>
                </h1>
                <p class="text-lg md:text-xl mb-10 max-w-2xl leading-relaxed" style="color: rgba(255,255,255,0.85);">
                    {{ $heroDescription }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('frontend.donate') }}" class="px-10 py-4 rounded-full font-extrabold text-lg shadow-lg transition-all text-center" style="background: #F68B1E; color: #ffffff;" onmouseover="this.style.background='#e07a1a';this.style.transform='scale(1.05)'" onmouseout="this.style.background='#F68B1E';this.style.transform='scale(1)'">Support us</a>
                    <a href="{{ route('frontend.about-us') }}" class="px-10 py-4 rounded-full font-extrabold text-lg border-2 transition-all text-center" style="background: transparent; color: #F68B1E; border-color: #F68B1E;" onmouseover="this.style.background='#F68B1E';this.style.color='#ffffff'" onmouseout="this.style.background='transparent';this.style.color='#F68B1E'">Learn more</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $contactSubtitle }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        {{ $contactTitle }}
                    </h2>

                    <div class="mt-8 space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-teal-700">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">Address</h4>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $address }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-teal-700">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">Phone</h4>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $phone }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-teal-700">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">Email</h4>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-teal-700">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">Office hours</h4>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $officeHours }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-xl shadow-teal-900/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $formSubtitle }}</p>
                        <h3 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">
                            {{ $formTitle }}
                        </h3>

                        <form class="mt-8 space-y-6" action="#" method="POST">
                            @csrf
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-slate-700">{{ $nameLabel }}</label>
                                    <input type="text" name="name" id="name" required
                                           class="mt-2 block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-teal-700 sm:text-sm">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-slate-700">{{ $emailLabel }}</label>
                                    <input type="email" name="email" id="email" required
                                           class="mt-2 block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-teal-700 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-semibold text-slate-700">{{ $subjectLabel }}</label>
                                <input type="text" name="subject" id="subject" required
                                       class="mt-2 block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-teal-700 sm:text-sm">
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-semibold text-slate-700">{{ $messageLabel }}</label>
                                <textarea name="message" id="message" rows="5" required
                                          class="mt-2 block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-teal-700 sm:text-sm"></textarea>
                            </div>
                            <div>
                                <button type="submit"
                                        class="rounded-full bg-teal-700 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                                    {{ $submitLabel }}
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 rounded-2xl bg-teal-50 p-4 text-sm text-teal-800 border border-teal-100">
                            <strong>Note:</strong> {{ $successMessage }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
