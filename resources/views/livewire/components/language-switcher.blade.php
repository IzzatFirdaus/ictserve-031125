<{{--=============================================================================Livewire 3.x + Volt 1.x Functional
	Component=============================================================================Original Class:
	app/Livewire/LanguageSwitcher.php Migrated To: resources/views/livewire/components/language-switcher.blade.php
	Migration Date: 2025-11-24 PR: fix/livewire-3-updates/comprehensive-audit-2025-11 Reason for Volt Conversion: -
	Simple presentational component with minimal state (currentLocale) - Single action (switchLanguage) - ideal for
	Volt's concise syntax - No complex dependencies, form validation, or file uploads - Easier maintenance with
	co-located template and logic - Demonstrates modern Laravel Livewire 3.x + Volt 1.x patterns Trace: N/A (simple
	utility component) Requirements: Bilingual support (Malay/English language
	switching)=============================================================================--}}>

	@volt
	@php
		use App\Services\BilingualSupportService;

		// State: current locale
		$currentLocale = fn(BilingualSupportService $service) => $service->getCurrentLocale();

		// Helper: get locales and display names on demand
		$locales = fn(BilingualSupportService $service) => $service->getSupportedLocales();
		$getDisplayName = fn(BilingualSupportService $service, string $locale) => $service->getLocaleDisplayName($locale);
	@endphp

	<div class="flex items-center gap-2">
		@foreach ($locales($this, app(BilingualSupportService::class)) as $locale)
			<button wire:click="switchLanguage('{{ $locale }}')"
				class="px-3 py-2 text-sm font-medium rounded-md transition-colors
					   {{ $currentLocale($this, app(BilingualSupportService::class)) === $locale ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
				style="min-width: 44px; min-height: 44px;"
				aria-label="{{ __('Switch to') }} {{ $getDisplayName($this, app(BilingualSupportService::class), $locale) }}"
				aria-current="{{ $currentLocale($this, app(BilingualSupportService::class)) === $locale ? 'true' : 'false' }}">
				{{ strtoupper($locale) }}
			</button>
		@endforeach
	</div>

	@php
		// Action: switch language
		function switchLanguage(BilingualSupportService $service, string $locale)
		{
			$service->switchLocale($locale);

			// Refresh the page to apply new locale
			$this->redirect(request()->header('Referer') ?? '/');
		}
	@endphp
	@endvolt