{{--
    Figma Button Component Examples for ICTServe
    Demonstrates: Hybrid Architecture, Bahasa Melayu, WCAG Compliance
--}}

<x-layouts.app>
    <div class="max-w-4xl mx-auto p-6 space-y-8">

        {{-- Page Header --}}
        <div class="border-b border-gray-200 pb-4">
            <h1 class="text-3xl font-heading font-bold text-gray-900">
                {{ __('Figma Button Component Examples') }}
            </h1>
            <p class="mt-2 text-gray-600">
                {{ __('Demonstrating Figma-generated components in ICTServe context') }}
            </p>
        </div>

        {{-- Basic Variants --}}
        <section class="space-y-4">
            <h2 class="text-xl font-heading font-semibold text-gray-900">
                {{ __('Button Variants') }}
            </h2>

            <div class="flex flex-wrap gap-4">
                <x-ui.figma-button variant="primary">
                    {{ __('Hantar Tiket') }}
                </x-ui.figma-button>

                <x-ui.figma-button variant="secondary">
                    {{ __('Batal') }}
                </x-ui.figma-button>

                <x-ui.figma-button variant="success">
                    {{ __('Lulus') }}
                </x-ui.figma-button>

                <x-ui.figma-button variant="danger">
                    {{ __('Padam') }}
                </x-ui.figma-button>
            </div>
        </section>

        {{-- Size Variants --}}
        <section class="space-y-4">
            <h2 class="text-xl font-heading font-semibold text-gray-900">
                {{ __('Button Sizes') }}
            </h2>

            <div class="flex flex-wrap items-center gap-4">
                <x-ui.figma-button variant="primary" size="sm">
                    {{ __('Kecil') }}
                </x-ui.figma-button>

                <x-ui.figma-button variant="primary" size="md">
                    {{ __('Sederhana') }}
                </x-ui.figma-button>

                <x-ui.figma-button variant="primary" size="lg">
                    {{ __('Besar') }}
                </x-ui.figma-button>
            </div>
        </section>

        {{-- ICTServe Hybrid Architecture Examples --}}
        <section class="space-y-4">
            <h2 class="text-xl font-heading font-semibold text-gray-900">
                {{ __('Hybrid Architecture Examples') }}
            </h2>

            {{-- Guest vs Authenticated States --}}
            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">
                    {{ __('Guest vs Authenticated Actions') }}
                </h3>

                @auth
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600">
                            {{ __('Logged in as: :name', ['name' => auth()->user()->name]) }}
                        </p>
                        <x-ui.figma-button variant="primary" wire:click="submitAsUser">
                            {{ __('Hantar sebagai :name', ['name' => auth()->user()->name]) }}
                        </x-ui.figma-button>
                    </div>
                @else
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600">
                            {{ __('Not logged in - Guest mode') }}
                        </p>
                        <x-ui.figma-button variant="secondary" wire:click="submitAsGuest">
                            {{ __('Hantar sebagai Tetamu') }}
                        </x-ui.figma-button>
                    </div>
                @endauth
            </div>
        </section>

        {{-- Livewire Integration Examples --}}
        <section class="space-y-4">
            <h2 class="text-xl font-heading font-semibold text-gray-900">
                {{ __('Livewire Integration') }}
            </h2>

            <div class="bg-blue-50 rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">
                    {{ __('Interactive Examples') }}
                </h3>

                {{-- Loading States --}}
                <div class="space-y-2">
                    <x-ui.figma-button
                        variant="primary"
                        wire:click="processTicket"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>{{ __('Proses Tiket') }}</span>
                        <span wire:loading>{{ __('Memproses...') }}</span>
                    </x-ui.figma-button>
                </div>

                {{-- Form Submission --}}
                <div class="space-y-2">
                    <x-ui.figma-button
                        variant="success"
                        type="submit"
                        form="helpdesk-form"
                    >
                        {{ __('Hantar Permohonan Bantuan') }}
                    </x-ui.figma-button>
                </div>
            </div>
        </section>

        {{-- WCAG Compliance Demo --}}
        <section class="space-y-4">
            <h2 class="text-xl font-heading font-semibold text-gray-900">
                {{ __('WCAG 2.2 AA Compliance') }}
            </h2>

            <div class="bg-green-50 rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">
                    {{ __('Accessibility Features') }}
                </h3>

                <ul class="text-sm text-gray-600 space-y-1">
                    <li>✅ {{ __('Minimum 44x44px touch targets') }}</li>
                    <li>✅ {{ __('4.5:1 color contrast ratio') }}</li>
                    <li>✅ {{ __('Visible focus indicators') }}</li>
                    <li>✅ {{ __('Keyboard navigation support') }}</li>
                    <li>✅ {{ __('Screen reader compatible') }}</li>
                </ul>

                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-700">
                        {{ __('Try keyboard navigation (Tab key):') }}
                    </p>
                    <div class="flex gap-2">
                        <x-ui.figma-button variant="primary" size="sm">{{ __('Button 1') }}</x-ui.figma-button>
                        <x-ui.figma-button variant="secondary" size="sm">{{ __('Button 2') }}</x-ui.figma-button>
                        <x-ui.figma-button variant="success" size="sm">{{ __('Button 3') }}</x-ui.figma-button>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-layouts.app>
