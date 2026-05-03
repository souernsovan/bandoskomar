<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['en', 'id', 'th', 'vi', 'km', 'ms'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');
        if ($locale && in_array($locale, $this->supported, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
