@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner" style="margin-bottom: 40px;">
    <div class="welcome-content">
        <h2 class="welcome-title">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! 🎉</h2>
    </div>
</div>


@endsection
