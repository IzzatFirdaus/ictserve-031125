{{--
/**
 * Component name: Update Profile Information Form
 * Description: Volt component for updating user profile information including name and email with verification
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-011.5 (Profile Management)
 * @trace D04 §6.1 (Layout Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
            {{ __('profile.information_title') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('profile.information_description') }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('profile.name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full min-h-11 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500" required autofocus autocomplete="name" :placeholder="__('profile.name_placeholder')" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('profile.email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full min-h-11 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-600 dark:text-slate-400">
                        {{ __('auth.email_unverified') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-slate-800">
                            {{ __('auth.resend_verification') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success-600 dark:text-success-400">
                            {{ __('auth.verification_link_sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="min-h-11 px-6 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500">{{ __('profile.save_changes') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('profile.update_success') }}
            </x-action-message>
        </div>
    </form>
</section>

