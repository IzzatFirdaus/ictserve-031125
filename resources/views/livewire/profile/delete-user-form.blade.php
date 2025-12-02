{{--
/**
 * Component name: Delete User Form
 * Description: Volt component for permanent account deletion with password confirmation
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

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-xl font-semibold text-slate-100">
            {{ __('profile.delete_account_title') }}
        </h2>

        <p class="mt-1 text-sm text-slate-300">
            {{ __('profile.delete_account_description') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="min-h-44"
    >{{ __('profile.delete_account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <h2 class="text-lg font-medium text-slate-100">
                {{ __('profile.delete_account_confirm_title') }}
            </h2>

            <p class="mt-1 text-sm text-slate-300">
                {{ __('profile.delete_account_confirm_description') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" :value="__('profile.current_password')" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    :placeholder="__('profile.current_password_placeholder')"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="min-h-44">
                    {{ __('common.cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3 min-h-44">
                    {{ __('profile.delete_account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
