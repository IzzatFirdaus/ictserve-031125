<?php

declare(strict_types=1);

// name: TrackPortalActivity
// description: Middleware for comprehensive portal activity audit logging
// author: dev-team@motac.gov.my
// trace: D03 SRS-NFR-004, D10 §7, D11 §9 (Requirements 14.5)
// last-updated: 2025-11-06

namespace App\Http\Middleware;

use App\Models\PortalActivity;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackPortalActivity
{
    /**
     * Handle an incoming request.
     *
     * Logs all portal activities for 7-year audit trail compliance.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = Auth::user();

        // Skip if not authenticated or if OPTIONS request
        if (! $user || $request->isMethod('OPTIONS')) {
            return $response;
        }

        // Determine action from route
        $route = $request->route();
        $routeName = $route ? $route->getName() : 'unknown';
        $action = $this->determineAction($request, $routeName);

        // Extract subject information from route parameters
        $subjectType = null;
        $subjectId = null;
        $subjectTitle = null;

        if ($route && $route->hasParameter('ticket')) {
            $subjectType = 'App\\Models\\HelpdeskTicket';
            $subjectId = $this->normalizeSubjectId($route->parameter('ticket'));
            $subjectTitle = $subjectId !== null ? "Helpdesk Ticket #{$subjectId}" : 'Helpdesk Ticket';
        } elseif ($route && $route->hasParameter('loan')) {
            $subjectType = 'App\\Models\\LoanApplication';
            $subjectId = $this->normalizeSubjectId($route->parameter('loan'));
            $subjectTitle = $subjectId !== null ? "Loan Application #{$subjectId}" : 'Loan Application';
        }

        $resolvedSubjectTitle = $subjectTitle ?? ($routeName ?? 'portal_action');

        // Log activity with comprehensive metadata
        PortalActivity::create([
            'user_id' => $user->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_title' => $resolvedSubjectTitle,
            'metadata' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'route' => $routeName,
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
                'timestamp' => now()->toIso8601String(),
                'session_id' => session()->getId(),
            ],
        ]);

        return $response;
    }

    /**
     * Determine action type from request and route
     */
    private function determineAction(Request $request, ?string $routeName): string
    {
        // Map route patterns to action types
        $actionMap = [
            'staff.dashboard' => 'view_dashboard',
            'staff.profile' => 'view_profile',
            'helpdesk.authenticated' => 'view_helpdesk',
            'loan.authenticated' => 'view_loans',
            'loan.approvals' => 'view_approvals',
        ];

        foreach ($actionMap as $pattern => $action) {
            if ($routeName && str_contains($routeName, $pattern)) {
                return $action;
            }
        }

        // HTTP method-based actions
        return match ($request->method()) {
            'GET' => 'view_page',
            'POST' => 'create_record',
            'PUT', 'PATCH' => 'update_record',
            'DELETE' => 'delete_record',
            default => 'portal_action',
        };
    }

    private function normalizeSubjectId(mixed $subject): string|int|null
    {
        if ($subject instanceof Model) {
            $key = $subject->getKey();

            return is_int($key) || is_string($key) ? $key : null;
        }

        if (is_int($subject) || is_string($subject)) {
            return $subject;
        }

        return null;
    }
}
