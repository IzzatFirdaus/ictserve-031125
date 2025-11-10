<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;

/**
 * ProfileController
 * Provides minimal PATCH endpoint for tests to update name & phone.
 * trace: D03-FR-011.3; D04 §4.2; D11 §6
 */
class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);
        // Validation: use explicit validator so we can reliably seed session error bag for tests
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'phone' => ['nullable', 'regex:/^[0-9\-()+ ]{7,20}$/'],
        ]);

        if ($validator->fails()) {
            $errorBag = new ViewErrorBag();
            $errorBag->put('default', $validator->errors());
            // Persist errors in session explicitly so assertSessionHasErrors() passes with array driver
            session(['errors' => $errorBag]);

            return redirect()->route('portal.profile')->withInput();
        }

        $validated = $validator->validated();

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        return back()->with('status', __('Profile updated'));
    }
}
