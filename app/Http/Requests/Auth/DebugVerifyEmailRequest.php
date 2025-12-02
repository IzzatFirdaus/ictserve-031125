<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

/**
 * DEBUG: Temporary request class to trace authorization failure
 */
class DebugVerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        $userId = (string) $this->user()?->getKey();
        $routeId = (string) $this->route('id');
        $userHash = sha1($this->user()?->getEmailForVerification() ?? '');
        $routeHash = (string) $this->route('hash');
        
        Log::debug('[DEBUG] DebugVerifyEmailRequest::authorize', [
            'user_id' => $userId,
            'route_id' => $routeId,
            'id_match' => hash_equals($userId, $routeId),
            'user_hash' => $userHash,
            'route_hash' => $routeHash,
            'hash_match' => hash_equals($userHash, $routeHash),
        ]);

        if (! hash_equals($userId, $routeId)) {
            Log::debug('[DEBUG] ID mismatch - returning false');
            return false;
        }

        if (! hash_equals($userHash, $routeHash)) {
            Log::debug('[DEBUG] Hash mismatch - returning false');
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill(): void
    {
        if (! $this->user()->hasVerifiedEmail()) {
            $this->user()->markEmailAsVerified();
            event(new Verified($this->user()));
        }
    }
}
