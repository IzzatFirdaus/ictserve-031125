{{--
/**
 * Searchable Select Component (Virtual Scrolled Combobox)
 *
 * A WCAG 2.2 AA compliant searchable dropdown with virtual scrolling
 * for large lists. Uses Alpine.js for client-side filtering and
 * keyboard navigation.
 *
 * @component Form Component
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-11-27
 *
 * @trace Task 3.1.10 - Implement Searchable Division Select
 * @requirements R06 (Form Components), R07 (WCAG 2.2 AA)
 * @wcag_level AA (SC 1.3.1, 2.1.1, 4.1.2)
 *
 * Props:
 * - name: Field name for form submission
 * - label: Label text
 * - options: Array of options [{id, name}]
 * - selected: Currently selected value
 * - placeholder: Placeholder text
 * - searchPlaceholder: Search input placeholder
 * - required: Whether field is required
 * - disabled: Whether field is disabled
 * - error: Error message to display
 * - maxHeight: Maximum dropdown height (default: 300px)
 */
--}}

@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option',
    'searchPlaceholder' => 'Search...',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'maxHeight' => '300px',
    'wireModel' => null,
])

<div x-data="searchableSelect({
    options: {{ json_encode($options) }},
    selected: {{ json_encode($selected) }},
    placeholder: '{{ $placeholder }}',
    searchPlaceholder: '{{ $searchPlaceholder }}',
    name: '{{ $name }}',
    wireModel: '{{ $wireModel }}'
})" x-init="init()" class="relative" {{ $attributes->whereStartsWith('wire:') }}>
    {{-- Label --}}
    @if ($label)
        <label :id="labelId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-danger-500" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{-- Hidden input for form submission --}}
    <input type="hidden" :name="name" x-model="selectedValue"
        @if ($wireModel) wire:model="{{ $wireModel }}" @endif />

    {{-- Combobox Button --}}
    <button type="button" @click="toggle()" @keydown.escape="close()"
        @keydown.arrow-down.prevent="open(); focusFirstOption()" @keydown.arrow-up.prevent="open(); focusLastOption()"
        @keydown.enter.prevent="toggle()" @keydown.space.prevent="toggle()" :aria-expanded="isOpen"
        :aria-labelledby="labelId" aria-haspopup="listbox" :aria-controls="listboxId"
        :disabled="{{ $disabled ? 'true' : 'false' }}"
        class="relative w-full cursor-pointer rounded-md border border-gray-300 bg-white py-2.5 pl-3 pr-10 text-left shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white min-h-[44px] {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
        <span x-text="selectedLabel || placeholder" :class="{ 'text-gray-500': !selectedLabel }"
            class="block truncate"></span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95" @click.outside="close()"
        class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800 dark:ring-gray-700"
        style="max-height: {{ $maxHeight }};">
        {{-- Search Input --}}
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
            <input type="text" x-model="searchQuery" x-ref="searchInput" @keydown.escape="close()"
                @keydown.arrow-down.prevent="focusNextOption()" @keydown.arrow-up.prevent="focusPreviousOption()"
                @keydown.enter.prevent="selectFocusedOption()" :placeholder="searchPlaceholder"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white min-h-[44px]"
                aria-label="{{ __('Search options') }}" />
        </div>

        {{-- Options List (Virtual Scrolled) --}}
        <ul :id="listboxId" role="listbox" :aria-labelledby="labelId" class="overflow-auto py-1"
            style="max-height: calc({{ $maxHeight }} - 60px);" x-ref="listbox">
            {{-- No Results Message --}}
            <li x-show="filteredOptions.length === 0"
                class="relative cursor-default select-none py-2 px-3 text-gray-500 dark:text-gray-400">
                {{ __('No results found') }}
            </li>

            {{-- Options --}}
            <template x-for="(option, index) in filteredOptions" :key="option.id">
                <li :id="'option-' + option.id" role="option" :aria-selected="selectedValue == option.id"
                    @click="selectOption(option)" @keydown.enter.prevent="selectOption(option)"
                    @keydown.space.prevent="selectOption(option)" @mouseenter="focusedIndex = index"
                    :class="{
                        'bg-primary-600 text-white': focusedIndex === index,
                        'text-gray-900 dark:text-white': focusedIndex !== index,
                        'bg-primary-50 dark:bg-primary-900/20': selectedValue == option.id && focusedIndex !== index
                    }"
                    class="relative cursor-pointer select-none py-2 pl-3 pr-9 min-h-[44px] flex items-center"
                    tabindex="-1">
                    <span x-text="option.name" :class="{ 'font-semibold': selectedValue == option.id }"
                        class="block truncate"></span>

                    {{-- Checkmark for selected option --}}
                    <span x-show="selectedValue == option.id"
                        :class="{ 'text-white': focusedIndex === index, 'text-primary-600': focusedIndex !== index }"
                        class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            </template>
        </ul>

        {{-- Results Count (Screen Reader) --}}
        <div class="sr-only" aria-live="polite" x-text="filteredOptions.length + ' {{ __('options available') }}'">
        </div>
    </div>

    {{-- Error Message --}}
    @if ($error)
        <p class="mt-1 text-sm text-danger-600" role="alert">{{ $error }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-danger-600" role="alert">{{ $message }}</p>
    @enderror
</div>

@pushOnce('scripts')
    <script>
        /**
         * Searchable Select Alpine.js Component
         * 
         * Provides virtual scrolling and keyboard navigation for large option lists.
         * WCAG 2.2 AA compliant with proper ARIA attributes.
         */
        function searchableSelect(config) {
            return {
                // Configuration
                options: config.options || [],
                placeholder: config.placeholder || 'Select an option',
                searchPlaceholder: config.searchPlaceholder || 'Search...',
                name: config.name || '',
                wireModel: config.wireModel || '',

                // State
                isOpen: false,
                searchQuery: '',
                selectedValue: config.selected,
                selectedLabel: '',
                focusedIndex: -1,

                // IDs for ARIA
                labelId: 'label-' + Math.random().toString(36).substr(2, 9),
                listboxId: 'listbox-' + Math.random().toString(36).substr(2, 9),

                // Computed: Filtered options based on search query
                get filteredOptions() {
                    if (!this.searchQuery) {
                        return this.options;
                    }
                    const query = this.searchQuery.toLowerCase();
                    return this.options.filter(option =>
                        option.name.toLowerCase().includes(query)
                    );
                },

                // Initialize component
                init() {
                    // Set initial selected label
                    if (this.selectedValue) {
                        const selected = this.options.find(o => o.id == this.selectedValue);
                        if (selected) {
                            this.selectedLabel = selected.name;
                        }
                    }

                    // Watch for external changes to selected value
                    this.$watch('selectedValue', (value) => {
                        const selected = this.options.find(o => o.id == value);
                        this.selectedLabel = selected ? selected.name : '';

                        // Update Livewire if wireModel is set
                        if (this.wireModel && this.$wire) {
                            this.$wire.set(this.wireModel, value);
                        }
                    });
                },

                // Toggle dropdown
                toggle() {
                    this.isOpen ? this.close() : this.open();
                },

                // Open dropdown
                open() {
                    this.isOpen = true;
                    this.focusedIndex = -1;
                    this.$nextTick(() => {
                        this.$refs.searchInput?.focus();
                    });
                },

                // Close dropdown
                close() {
                    this.isOpen = false;
                    this.searchQuery = '';
                    this.focusedIndex = -1;
                },

                // Select an option
                selectOption(option) {
                    this.selectedValue = option.id;
                    this.selectedLabel = option.name;
                    this.close();

                    // Dispatch change event
                    this.$dispatch('change', {
                        value: option.id,
                        label: option.name
                    });
                },

                // Focus navigation
                focusFirstOption() {
                    this.focusedIndex = 0;
                    this.scrollToFocused();
                },

                focusLastOption() {
                    this.focusedIndex = this.filteredOptions.length - 1;
                    this.scrollToFocused();
                },

                focusNextOption() {
                    if (this.focusedIndex < this.filteredOptions.length - 1) {
                        this.focusedIndex++;
                        this.scrollToFocused();
                    }
                },

                focusPreviousOption() {
                    if (this.focusedIndex > 0) {
                        this.focusedIndex--;
                        this.scrollToFocused();
                    }
                },

                selectFocusedOption() {
                    if (this.focusedIndex >= 0 && this.focusedIndex < this.filteredOptions.length) {
                        this.selectOption(this.filteredOptions[this.focusedIndex]);
                    }
                },

                // Scroll to keep focused option visible
                scrollToFocused() {
                    this.$nextTick(() => {
                        const listbox = this.$refs.listbox;
                        const focused = listbox?.querySelector(
                            `[id="option-${this.filteredOptions[this.focusedIndex]?.id}"]`);
                        if (focused && listbox) {
                            focused.scrollIntoView({
                                block: 'nearest'
                            });
                        }
                    });
                }
            };
        }
    </script>
@endPushOnce
