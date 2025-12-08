<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocale Middleware
 *
 * @deprecated v3.6.0 Language switching disabled. System now uses Bahasa Melayu only.
 *             This middleware is retained for backward compatibility but always sets
 *             locale to 'ms' regardless of user preferences or cookies.
 *             English translation files in lang/en/ are retained for technical reference.
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * v3.6.0: Always sets locale to 'ms' (Bahasa Melayu).
     * User preferences and cookies are ignored.
     * The ictserve_locale cookie is deleted to clean up legacy data.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // v3.6.0: Always use Bahasa Melayu - ignore user preferences and cookies
        App::setLocale('ms');

        $response = $next($request);

        // v3.6.0: Delete legacy ictserve_locale cookie if present
        if ($request->hasCookie('ictserve_locale')) {
            $response->headers->setCookie(
                Cookie::forget('ictserve_locale')
            );
        }

        return $response;
    }
}
