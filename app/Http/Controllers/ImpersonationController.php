<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(User $user)
    {
        // Only admins can impersonate
        // Assuming 'Super Admin' role exists, or check specific permission
        if (!auth()->user()->can('impersonate users') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent impersonating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Prevent impersonating other admins (optional security measure)
        if ($user->hasRole('Super Admin')) {
             return back()->with('error', 'You cannot impersonate another Super Admin.');
        }

        session()->put('impersonator_id', auth()->id());
        Auth::login($user);

        return redirect()->route('portal.dashboard');
    }

    public function stop()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('portal.dashboard');
        }

        Auth::loginUsingId(session('impersonator_id'));
        session()->forget('impersonator_id');

        return redirect()->to('/admin'); // Redirect back to Filament admin
    }
}
