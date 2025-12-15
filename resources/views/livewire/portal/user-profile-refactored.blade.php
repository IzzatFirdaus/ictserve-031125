<?php

use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Validation\Rule;
use function Livewire\Volt\{state, rules, computed};

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $department = '';
    public bool $showSuccessMessage = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->department = $user->department ?? '';
    }

    #[Validate]
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(auth()->id())],
            'phone' => ['nullable', 'string', 'regex:/^[0-9\-\+\s]+$/', 'max:20'],
            'department' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'department' => $this->department,
        ]);

        $this->showSuccessMessage = true;
        $this->dispatch('profile-updated');

        // Hide success message after 5 seconds
        $this->js('setTimeout(() => window.Livewire.find(this.$el.closest("[wire\\:id]").__livewire.id).call("hideSuccessMessage"), 5000)');
    }

    public function hideSuccessMessage(): void
    {
        $this->showSuccessMessage = false;
    }
};

?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8 theme-transition">
    <main id="main-content" class="max-w-2xl mx-auto" tabindex="-1">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-heading font-semibold text-gray-900 dark:text-white">
                {{ __('profile.edit_title') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('profile.edit_subtitle') }}
            </p>
        </div>

        <!-- Success Message -->
        @if($showSuccessMessage)
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 rounded-m"
                 role="alert"
                 aria-live="polite">
                <div class="flex items-center">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0" aria-hidden="true" />
                    <div>
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ __('profile.update_success') }}
                        </h3>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-l shadow-card overflow-hidden">
            <form wire:submit="save" class="p-6 sm:p-8">
                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        {{ __('profile.name') }}
                        <span class="text-danger-600" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        wire:model.live.debounce.300ms="name"
                        class="form-input block w-full rounded-m border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               shadow-sm focus:border-primary-500 focus:ring-primary-500
                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                               min-h-11 px-4 py-2 transition-colors duration-200"
                        aria-required="true"
                        aria-describedby="name-error"
                        autocomplete="name"
                    />
                    @error('name')
                        <p id="name-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        {{ __('profile.email') }}
                        <span class="text-danger-600" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        wire:model.live.debounce.300ms="email"
                        class="form-input block w-full rounded-m border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               shadow-sm focus:border-primary-500 focus:ring-primary-500
                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                               min-h-11 px-4 py-2 transition-colors duration-200"
                        aria-required="true"
                        aria-describedby="email-error email-help"
                        autocomplete="email"
                    />
                    <p id="email-help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                        {{ __('profile.email_help') }}
                    </p>
                    @error('email')
                        <p id="email-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        {{ __('profile.phone') }}
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        wire:model.live.debounce.300ms="phone"
                        class="form-input block w-full rounded-m border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               shadow-sm focus:border-primary-500 focus:ring-primary-500
                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                               min-h-11 px-4 py-2 transition-colors duration-200"
                        aria-describedby="phone-error phone-help"
                        autocomplete="tel"
                    />
                    <p id="phone-help" class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                        {{ __('profile.phone_help') }}
                    </p>
                    @error('phone')
                        <p id="phone-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Department Field -->
                <div class="mb-8">
                    <label for="department" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        {{ __('profile.department') }}
                    </label>
                    <input
                        type="text"
                        id="department"
                        wire:model.live.debounce.300ms="department"
                        class="form-input block w-full rounded-m border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               shadow-sm focus:border-primary-500 focus:ring-primary-500
                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                               min-h-11 px-4 py-2 transition-colors duration-200"
                        aria-describedby="department-error"
                    />
                    @error('department')
                        <p id="department-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="btn-primary min-h-11 px-6 py-3 rounded-m shadow-button
                               bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700
                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                               transition-colors duration-200 font-medium inline-flex items-center justify-center"
                        wire:loading.attr="disabled"
                        aria-label="{{ __('profile.save_changes') }}">
                        <span wire:loading.remove>{{ __('profile.save_changes') }}</span>
                        <span wire:loading>
                            <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" aria-hidden="true" />
                            {{ __('common.saving') }}
                        </span>
                    </button>

                    <a href="{{ route('dashboard') }}"
                       class="btn-secondary min-h-11 px-6 py-3 rounded-m shadow-button
                              bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white
                              hover:bg-gray-300 dark:hover:bg-gray-600
                              focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:outline-none
                              transition-colors duration-200 font-medium inline-flex items-center justify-center"
                       wire:navigate>
                        {{ __('common.cancel') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900 rounded-m p-4 border border-blue-200 dark:border-blue-700">
            <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">
                {{ __('profile.security_info') }}
            </h3>
            <p class="text-sm text-blue-700 dark:text-blue-200">
                {{ __('profile.security_message') }}
            </p>
        </div>
    </main>
</div>
