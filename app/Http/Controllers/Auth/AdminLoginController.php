<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminLoginController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes'],
        ]);

        $email = strtolower(trim($data['email']));
        if (! str_contains($email, '@')) {
            $email = $email.'@motac.gov.my';
        }

        Log::info('Admin login POST attempt', ['email' => $email]);

        if (! Auth::attempt(['email' => $email, 'password' => $data['password']], isset($data['remember']) && $data['remember'])) {
            return redirect()->back()->withInput()->withErrors(['email' => 'Kredensial tidak sah.']);
        }

        $user = Auth::user();
        if (! $user || ! $user->hasAnyRole(['admin', 'superuser'])) {
            Auth::logout();

            return redirect()->back()->withInput()->withErrors(['email' => 'Anda tidak mempunyai kebenaran untuk mengakses panel pentadbir.']);
        }

        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }
}
