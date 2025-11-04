<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\SecurityMonitoringService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Monitoring Middleware
 *
 * Monitors requests for suspicious activities and security threats.
 * Integrates with SecurityMonitoringService for comprehensive monitoring.
 *
 * @see D03-FR-010.1 Security monitoring requirements
 * @see D11 Technical Design - Security middleware
 */
class SecurityMonitoringMiddleware
{
    public function __construct(
        private SecurityMonitoringService $securityMonitoring
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for blocked IP
        if ($this->securityMonitoring->isIpBlocked($request->ip())) {
            $this->securityMonitoring->logSuspiciousActivity(
                'Blocked IP attempted access',
                ['ip' => $request->ip()],
                $request
            );

            abort(429, 'Too many failed attempts. Please try again later.');
        }

        // Monitor for suspicious patterns
        $this->monitorSuspiciousPatterns($request);

        // Monitor API rate limiting for API routes
        if ($request->is('api/*')) {
            $identifier = $request->ip();
            if (auth()->check()) {
                $identifier = 'user:' . auth()->id();
            }

            if (!$this->securityMonitoring->monitorApiRateLimit($identifier)) {
                abort(429, 'Rate limit exceeded');
            }
        }

        $response = $next($request);

        // Log security-relevant responses
        $this->logSecurityRelevantResponses($request, $response);

        return $response;
    }

    /**
     * Monitor for suspicious patterns
     */
    private function monitorSuspiciousPatterns(Request $request): void
    {
        // Check for SQL injection patterns
        $this->checkSqlInjectionPatterns($request);

        // Check for XSS patterns
        $this->checkXssPatterns($request);

        // Check for suspicious user agents
        $this->checkSuspiciousUserAgent($request);

        // Check for unusual request patterns
        $this->checkUnusualRequestPatterns($request);
    }

    /**
     * Check for SQL injection patterns
     */
    private function checkSqlInjectionPatterns(Request $request): void
    {
        $sqlPatterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b.*\bWHERE\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bOR\b.*1\s*=\s*1)/i',
            '/(\bAND\b.*1\s*=\s*1)/i',
        ];

        $allInput = array_merge($request->all(), [$request->getRequestUri()]);

        foreach ($allInput as $input) {
            if (is_string($input)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $input)) {
                        $this->securityMonitoring->logSuspiciousActivity(
                            'Potential SQL injection attempt',
                            [
                                'pattern' => $pattern,
                                'input' => substr($input, 0, 200), // Limit logged input
                                'url' => $request->url(),
                            ],
                            $request
                        );
                        break;
                    }
                }
            }
        }
    }

    /**
     * Check for XSS patterns
     */
    private function checkXssPatterns(Request $request): void
    {
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
        ];

        foreach ($request->all() as $input) {
            if (is_string($input)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $input)) {
                        $this->securityMonitoring->logSuspiciousActivity(
                            'Potential XSS attempt',
                            [
                                'pattern' => $pattern,
                                'input' => substr($input, 0, 200),
                                'url' => $request->url(),
                            ],
                            $request
                        );
                        break;
                    }
                }
            }
        }
    }

    /**
     * Check for suspicious user agents
     */
    private function checkSuspiciousUserAgent(Request $request): void
    {
        $userAgent = $request->userAgent();

        $suspiciousPatterns = [
            '/bot/i',
            '/crawler/i',
            '/spider/i',
            '/scraper/i',
            '/curl/i',
            '/wget/i',
            '/python/i',
            '/perl/i',
        ];

        // Skip monitoring for legitimate bots (optional)
        $legitimateBots = [
            '/googlebot/i',
            '/bingbot/i',
            '/slurp/i', // Yahoo
        ];

        $isLegitimate = false;
        foreach ($legitimateBots as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $isLegitimate = true;
                break;
            }
        }

        if (!$isLegitimate) {
            foreach ($suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $userAgent)) {
                    $this->securityMonitoring->logSuspiciousActivity(
                        'Suspicious user agent detected',
                        [
                            'user_agent' => $userAgent,
                            'pattern' => $pattern,
                        ],
                        $request
                    );
                    break;
                }
            }
        }
    }

    /**
     * Check for unusual request patterns
     */
    private function checkUnusualRequestPatterns(Request $request): void
    {
        // Check for unusually long URLs
        if (strlen($request->getRequestUri()) > 2000) {
            $this->securityMonitoring->logSuspiciousActivity(
                'Unusually long URL detected',
                [
                    'url_length' => strlen($request->getRequestUri()),
                    'url' => substr($request->getRequestUri(), 0, 500),
                ],
                $request
            );
        }

        // Check for unusual number of parameters
        if (count($request->all()) > 50) {
            $this->securityMonitoring->logSuspiciousActivity(
                'Unusual number of parameters',
                [
                    'parameter_count' => count($request->all()),
                    'url' => $request->url(),
                ],
                $request
            );
        }

        // Check for binary data in parameters
        foreach ($request->all() as $key => $value) {
            if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                $this->securityMonitoring->logSuspiciousActivity(
                    'Binary data in parameters',
                    [
                        'parameter' => $key,
                        'url' => $request->url(),
                    ],
                    $request
                );
            }
        }
    }

    /**
     * Log security-relevant responses
     */
    private function logSecurityRelevantResponses(Request $request, Response $response): void
    {
        $statusCode = $response->getStatusCode();

        // Log security-relevant status codes
        if (in_array($statusCode, [401, 403, 404, 429, 500])) {
            $this->securityMonitoring->logSecurityEvent('Security-relevant response', [
                'status_code' => $statusCode,
                'url' => $request->url(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Log failed authentication attempts (401)
        if ($statusCode === 401) {
            $this->securityMonitoring->logSuspiciousActivity(
                'Unauthorized access attempt',
                [
                    'url' => $request->url(),
                    'method' => $request->method(),
                ],
                $request
            );
        }
    }
}
