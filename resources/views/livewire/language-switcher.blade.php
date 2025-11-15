<div class="flex items-center gap-2">
    @foreach ($locales as $locale)
        <button wire:click="switchLanguage('{{ $locale }}')"
            class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                   {{ $currentLocale === $locale ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            style="min-width: 44px; min-height: 44px;" aria-label="{{ __('Switch to') }} {{ $getDisplayName($locale) }}"
            aria-current="{{ $currentLocale === $locale ? 'true' : 'false' }}">
            {{ strtoupper($locale) }}
        </button>
    @endforeach
</div>
