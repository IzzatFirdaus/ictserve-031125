<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect Alias Resources Middleware
 *
 * Redirects deprecated alias resource URLs to their canonical counterparts.
 * Uses HTTP 301 permanent redirect to ensure proper SEO and bookmark handling.
 *
 * @trace D04-§4.2 (URL routing and resource management)
 */
class RedirectAliasResources
{
    /**
     * Alias to canonical URL mappings.
     *
     * @var array<string, string>
     */
    protected array $redirectMap = [
        '/admin/operations/loans/loan-applications' => '/admin/operations/loan-applications',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        foreach ($this->redirectMap as $aliasPath => $canonicalPath) {
            if ($this->matchesAliasPath($path, $aliasPath)) {
                $newPath = $this->buildCanonicalUrl($path, $aliasPath, $canonicalPath);
                $newUrl = $request->getSchemeAndHttpHost().$newPath;

                // Preserve query parameters
                if ($request->getQueryString()) {
                    $newUrl .= '?'.$request->getQueryString();
                }

                // Log the redirect for monitoring
                Log::info('Alias resource redirect', [
                    'from' => $request->fullUrl(),
                    'to' => $newUrl,
                    'user_id' => $request->user()?->id,
                ]);

                return redirect($newUrl, 301);
            }
        }

        return $next($request);
    }

    /**
     * Check if the current path matches an alias path.
     */
    protected function matchesAliasPath(string $currentPath, string $aliasPath): bool
    {
        // Exact match or starts with alias path (for sub-routes like /edit, /create)
        return $currentPath === $aliasPath || str_starts_with($currentPath, $aliasPath.'/');
    }

    /**
     * Build the canonical URL from the alias path.
     */
    protected function buildCanonicalUrl(string $currentPath, string $aliasPath, string $canonicalPath): string
    {
        // Replace the alias prefix with the canonical prefix
        if ($currentPath === $aliasPath) {
            return $canonicalPath;
        }

        // Handle sub-routes (e.g., /admin/loans/loan-applications/123/edit -> /admin/loan-applications/123/edit)
        $suffix = substr($currentPath, \strlen($aliasPath));

        return $canonicalPath.$suffix;
    }
}
