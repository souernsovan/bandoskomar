@extends('frontend.layouts.app')
@section('content')

<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @for($i=1;$i<=9;$i++)
                <div class="rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-sm">
                    <div class="aspect-video bg-slate-200 flex items-center justify-center text-slate-400 text-sm">Image {{ $i }}</div>
                </div>
            @endfor
        </div>
    </div>
</section>
@endsection
