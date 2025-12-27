<?php

namespace App\Livewire\Staff;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SessionManager extends Component
{
    public function getSessionsProperty()
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            return (object) [
                'agent' => $this->createAgent($session),
                'ip_address' => $session->ip_address,
                'is_current_device' => $this->isCurrentSession($session->id),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Check if the given session ID is the current session.
     */
    protected function isCurrentSession(string $sessionId): bool
    {
        try {
            $request = request();
            if ($request->hasSession()) {
                return $sessionId === $request->session()->getId();
            }
        } catch (\RuntimeException $e) {
            // Session store not set on request - this can happen in tests
        }

        return false;
    }

    /**
     * Get the current session ID safely.
     */
    protected function getCurrentSessionId(): ?string
    {
        try {
            $request = request();
            if ($request->hasSession()) {
                return $request->session()->getId();
            }
        } catch (\RuntimeException $e) {
            // Session store not set on request - this can happen in tests
        }

        return null;
    }

    protected function createAgent($session)
    {
        return tap(new \stdClass, function ($agent) use ($session) {
            $agent->is_desktop = preg_match('/(Windows|Macintosh|Linux)/i', $session->user_agent);
            $agent->platform = $this->platform($session->user_agent);
            $agent->browser = $this->browser($session->user_agent);
        });
    }

    protected function platform($userAgent)
    {
        if (preg_match('/windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        }

        return 'Unknown';
    }

    protected function browser($userAgent)
    {
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Edge|Edg/i', $userAgent)) {
            return 'Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            return 'Opera';
        }

        return 'Unknown';
    }

    public function logoutOtherBrowserSessions()
    {
        $currentSessionId = $this->getCurrentSessionId();

        $query = DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->getAuthIdentifier());

        if ($currentSessionId) {
            $query->where('id', '!=', $currentSessionId);
        }

        $query->delete();

        $this->dispatch('logged-out-other-devices');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.staff.session-manager');
    }
}
