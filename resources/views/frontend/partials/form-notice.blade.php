{{-- Result box for a form. Expects $form (form key) and $id. Filled by JS, or by the session after a no-JS submit. --}}
@php
    $status = session('form_status');
    $mine = is_array($status) && ($status['form'] ?? null) === $form;
    $message = $mine ? $status['message'] : ($errors->any() && old('_form') === $form ? $errors->first() : '');
    $ok = $mine && $status['ok'];
@endphp
<p class="notice {{ $message !== '' ? 'show' : '' }} {{ $message !== '' && !$ok ? 'error' : '' }}" id="{{ $id }}" role="status" aria-live="polite">{{ $message }}</p>
