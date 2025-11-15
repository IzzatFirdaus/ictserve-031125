<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\BilingualSupportService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(
        private BilingualSupportService $bilingualService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->bilingualService->detectLocale();
        $this->bilingualService->setLocale($locale);

        return $next($request);
    }
}
