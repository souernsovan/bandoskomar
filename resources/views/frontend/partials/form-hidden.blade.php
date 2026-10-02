{{-- CSRF token, form key and an invisible spam trap. Expects $form. --}}
@csrf
<input type="hidden" name="_form" value="{{ $form }}">
<input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
