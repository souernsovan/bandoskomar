<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Raises PHP execution budget for page admin saves that include many uploaded files.
 */
class ExtendPageMultipartLimits
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true) && $request->files->count() > 0) {
            $seconds = (int) config('page_uploads.max_execution_seconds', 600);
            if ($seconds > 0) {
                @set_time_limit($seconds);
                @ini_set('max_execution_time', (string) $seconds);
            }
        }

        return $next($request);
    }
}
