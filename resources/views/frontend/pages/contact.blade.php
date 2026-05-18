@extends('frontend.layouts.app')

@php
    $contactContent = $contactContent ?? [];
    $contactLabels = $contactLabels ?? ($contactContent['labels'] ?? []);
    $contactPlaceholders = $contactPlaceholders ?? ($contactContent['placeholders'] ?? []);
    $contactMessages = $contactMessages ?? ($contactContent['messages'] ?? []);
    $contactMethods = collect($contactMethods ?? [])->filter(fn ($method) => !empty($method['enabled']) && !empty($method['value']))->values();

    $pageTitle = trim((string) ($contactContent['page_title'] ?? '')) ?: ($page->getTitleForLocale() ?: 'Contact Us');
    $intro = trim((string) ($contactContent['page_intro'] ?? ''));
    if ($intro === '') {
        $intro = trim((string) ($page->getContentForLocale() ?? ''));
    }
    if ($intro === '') {
        $intro = "We're here to help. Feel free to reach out to us through any of the channels below or send us a message directly.";
    }
    $infoTitle = trim((string) ($contactContent['contact_info_title'] ?? '')) ?: 'Contact Information';
    $confirmOpen = $contactContent['confirm_open'] ?? 'Open this contact method?';
    $formTitle = trim((string) ($contactContent['form_title'] ?? '')) ?: 'Send Us a Message';
    $formSubtitle = trim((string) ($contactContent['form_subtitle'] ?? '')) ?: 'Fill out the form below and we will get back to you as soon as possible.';
    $successMessage = trim((string) ($contactContent['success_message'] ?? '')) ?: 'Thank you! Your message has been sent and we will respond shortly.';
    $address = trim((string) ($contactContent['address'] ?? ''));
    $phone = trim((string) ($contactContent['phone'] ?? ''));
    $email = trim((string) ($contactContent['email'] ?? ''));
    $officeHours = trim((string) ($contactContent['office_hours'] ?? ''));
    $noMethodsMessage = trim((string) ($contactMessages['no_methods'] ?? '')) ?: 'No contact methods are available yet.';
@endphp

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const message = @json($confirmOpen);
            document.querySelectorAll('[data-contact-link]').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    const href = link.getAttribute('href');
                    if (!href || href === '#') {
                        event.preventDefault();
                        return;
                    }
                    if (!window.confirm(message)) {
                        event.preventDefault();
                        return;
                    }
                    if (/^https?:\/\//i.test(href)) {
                        event.preventDefault();
                        window.open(href, '_blank', 'noopener');
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <section
        class="relative overflow-hidden text-white"
        style="background: linear-gradient(180deg, var(--fe-header-bg) 0%, var(--fe-header-bg-deep) 62%, #16224f 100%);"
    >
        <div class="absolute inset-0">
            <div class="absolute -left-28 top-12 h-72 w-72 rounded-full bg-orange-500/20 blur-3xl"></div>
            <div class="absolute right-0 top-0 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,122,24,0.16),_transparent_42%),radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.08),_transparent_36%)]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="max-w-3xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-orange-100">
                    Contact
                </span>
                <h1 class="mt-6 max-w-3xl text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $pageTitle }}
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
                    {{ $intro }}
                </p>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        @if (session('success'))
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-900 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-8 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-900 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr]">
            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl shadow-slate-900/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-700">{{ $infoTitle }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $contactMethods->isNotEmpty() ? 'Direct contact channels' : 'Contact details' }}
                    </h2>

                    <div class="mt-8 space-y-5">
                        @forelse ($contactMethods as $method)
                            <a href="{{ $method['url'] }}" class="flex items-start gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-orange-200 hover:bg-orange-50/70" data-contact-link aria-label="{{ $method['label'] }}: {{ $method['value'] }}">
                                <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                                    @switch($method['key'])
                                        @case('email')
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 7.5v9a2.25 2.25 0 0 1-2.25 2.25H4.5A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25H4.5A2.25 2.25 0 0 0 2.25 7.5m19.5 0-9 6.75-9-6.75" />
                                            </svg>
                                            @break
                                        @case('phone')
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5.25A2.25 2.25 0 0 1 5.25 3h2.36a1.5 1.5 0 0 1 1.42 1.02l1.39 4.18a1.5 1.5 0 0 1-.54 1.66l-1.72 1.29a12.044 12.044 0 0 0 5.63 5.63l1.29-1.72a1.5 1.5 0 0 1 1.66-.54l4.18 1.39a1.5 1.5 0 0 1 1.02 1.42v2.36A2.25 2.25 0 0 1 18.75 21h-1.5C9.28 21 3 14.72 3 7.5v-2.25Z" />
                                            </svg>
                                            @break
                                        @case('whatsapp')
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.75a8.25 8.25 0 0 0-7.18 12.32L3.75 20.25l4.18-1.08A8.25 8.25 0 1 0 12 3.75Zm-2.25 5.25c.28 1.58 1.22 3.12 2.56 4.46 1.34 1.34 2.88 2.28 4.46 2.56l.75-1.5c.18-.37.07-.82-.26-1.07l-1.32-1.01a.75.75 0 0 0-.86-.03l-.86.54a6.75 6.75 0 0 1-2.76-2.76l.54-.86a.75.75 0 0 0-.03-.86l-1.01-1.32a.75.75 0 0 0-1.07-.26l-1.5.75Z" />
                                            </svg>
                                            @break
                                        @case('telegram')
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21.75 3.75-9.3 16.05a.75.75 0 0 1-1.35-.13l-2.14-6.42-6.42-2.14a.75.75 0 0 1-.13-1.35L18.75 2.25a.75.75 0 0 1 1.08.58l1.92 19.02Z" />
                                            </svg>
                                            @break
                                        @case('website')
                                        @case('link')
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.5 10.5a4.5 4.5 0 0 1 0 6.364l-2.26 2.26a4.5 4.5 0 0 1-6.364-6.364l1.06-1.06m6.364 0-1.06 1.06m1.06-1.06 2.26-2.26a4.5 4.5 0 1 1 6.364 6.364l-1.06 1.06m-6.364 0 1.06-1.06" />
                                            </svg>
                                            @break
                                        @default
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 9.75h7.5m-7.5 3h4.5M8.25 3.75h7.5A4.5 4.5 0 0 1 20.25 8.25v7.5a4.5 4.5 0 0 1-4.5 4.5H8.25L3.75 21l1.5-4.5v-8.25a4.5 4.5 0 0 1 3-4.5Z" />
                                            </svg>
                                    @endswitch
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-slate-900">{{ $method['label'] }}</span>
                                    <span class="mt-1 block break-words text-sm leading-6 text-slate-600">{{ $method['value'] }}</span>
                                </span>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-600">
                                {{ $noMethodsMessage }}
                            </div>
                        @endforelse
                    </div>

                    @if ($address !== '' || $officeHours !== '' || $phone !== '' || $email !== '')
                        <div class="mt-8 rounded-2xl bg-orange-50 p-5 ring-1 ring-orange-100">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Quick details</p>
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                                @if ($address !== '')
                                    <p><span class="font-semibold text-slate-900">Address:</span> {{ $address }}</p>
                                @endif
                                @if ($phone !== '')
                                    <p><span class="font-semibold text-slate-900">Phone:</span> {{ $phone }}</p>
                                @endif
                                @if ($email !== '')
                                    <p><span class="font-semibold text-slate-900">Email:</span> {{ $email }}</p>
                                @endif
                                @if ($officeHours !== '')
                                    <p><span class="font-semibold text-slate-900">Office hours:</span> {{ $officeHours }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                @if ($successMessage !== '')
                    <div class="rounded-[2rem] border border-orange-100 bg-orange-50 p-6 text-sm leading-6 text-orange-900 shadow-sm">
                        <strong class="block text-base font-semibold">What happens next</strong>
                        <p class="mt-2">{{ $successMessage }}</p>
                    </div>
                @endif
            </aside>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl shadow-slate-900/5 sm:p-10">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-700">{{ $formSubtitle }}</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">
                    {{ $formTitle }}
                </h2>

                <form action="{{ route('frontend.contact.send') }}" method="POST" class="mt-8 space-y-6">
                    @csrf
                    <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="contact_name" class="block text-sm font-semibold text-slate-700">
                                {{ $contactLabels['full_name'] ?? 'Full Name' }}
                            </label>
                            <input type="text" id="contact_name" name="name" value="{{ old('name') }}"
                                placeholder="{{ $contactPlaceholders['full_name'] ?? 'Your full name' }}" required
                                class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 transition placeholder:text-slate-400 focus:border-orange-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                            @error('name')
                                <span class="mt-2 block text-sm font-medium text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-semibold text-slate-700">
                                {{ $contactLabels['email_address'] ?? 'Email Address' }}
                            </label>
                            <input type="email" id="contact_email" name="email" value="{{ old('email') }}"
                                placeholder="{{ $contactPlaceholders['email_address'] ?? 'you@example.com' }}" required
                                class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 transition placeholder:text-slate-400 focus:border-orange-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                            @error('email')
                                <span class="mt-2 block text-sm font-medium text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="contact_message" class="block text-sm font-semibold text-slate-700">
                            {{ $contactLabels['message'] ?? 'Message' }}
                        </label>
                        <textarea id="contact_message" name="message" rows="8"
                            placeholder="{{ $contactPlaceholders['message'] ?? 'How can we help you?' }}" required
                            class="mt-2 block w-full rounded-3xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 transition placeholder:text-slate-400 focus:border-orange-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/30">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="mt-2 block text-sm font-medium text-rose-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm leading-6 text-slate-500">
                            We will review your message and respond as soon as we can.
                        </p>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-orange-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/25 transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-white">
                            {{ $contactLabels['send_message'] ?? 'Send Message' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
