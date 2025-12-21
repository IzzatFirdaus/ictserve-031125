<div class="widget-customization-panel" x-data="{
    isOpen: @entangle('isOpen'),
    activeTab: @entangle('activeTab'),
    showExportModal: @entangle('showExportModal'),
    showImportModal: @entangle('showImportModal'),
    showResetConfirmation: @entangle('showResetConfirmation')
}" x-init="// Initialize SortableJS for drag-and-drop
$nextTick(() => {
    if (window.Sortable) {
        initializeWidgetSorting();
    }
});

// Listen for Livewire events
$wire.on('panel-opened', () => {
    $nextTick(() => {
        if (window.Sortable) {
            initializeWidgetSorting();
        }
    });
});

$wire.on('copy-to-clipboard', (event) => {
    navigator.clipboard.writeText(event.data).then(() => {
        $wire.dispatch('notify', { message: 'Data telah disalin ke clipboard.' });
    });
});">

    <!-- Toggle Button -->
    <button type="button" @click="$wire.togglePanel()"
        class="fixed bottom-6 right-6 z-50 bg-primary-600 hover:bg-primary-700 text-white p-4 rounded-full shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        :class="{ 'bg-primary-700': isOpen }" aria-label="Buka panel penyesuaian widget" aria-expanded="false"
        :aria-expanded="isOpen.toString()">
        <svg class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-45': isOpen }" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
        </svg>
    </button>

    <!-- Customization Panel -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full"
        class="fixed inset-y-0 right-0 z-40 w-96 bg-white dark:bg-gray-800 shadow-2xl border-l border-gray-200 dark:border-gray-700 overflow-hidden"
        role="dialog" aria-modal="true" aria-labelledby="customization-panel-title">

        <!-- Panel Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 id="customization-panel-title" class="text-lg font-semibold text-gray-900 dark:text-white">
                Penyesuaian Widget
            </h2>
            <button type="button" @click="$wire.togglePanel()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-md p-1"
                aria-label="Tutup panel penyesuaian">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Tab Navigation -->
        <nav class="flex border-b border-gray-200 dark:border-gray-700" role="tablist"
            aria-label="Navigasi tab penyesuaian widget">
            @foreach (['layout' => 'Susun Atur', 'visibility' => 'Keterlihatan', 'sizes' => 'Saiz', 'import-export' => 'Import/Export'] as $tab => $label)
                <button type="button" @click="$wire.switchTab('{{ $tab }}')"
                    class="flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset"
                    :class="{
                        'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === '{{ $tab }}',
                        'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== '{{ $tab }}'
                    }"
                    role="tab" :aria-selected="(activeTab === '{{ $tab }}').toString()"
                    aria-controls="panel-{{ $tab }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>

        <!-- Panel Content -->
        <div class="flex-1 overflow-y-auto p-6">

            <!-- Layout Tab -->
            <div x-show="activeTab === 'layout'" role="tabpanel" id="panel-layout" aria-labelledby="tab-layout">
                <div class="space-y-6">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Seret dan lepas widget untuk menyusun semula kedudukan mereka dalam setiap kategori.
                    </div>

                    @foreach ($categories as $category)
                        <div class="space-y-3">
                            <h3 class="font-medium text-gray-900 dark:text-white">
                                {{ $this->getCategoryDisplayName($category) }}
                            </h3>

                            <div class="sortable-container bg-gray-50 dark:bg-gray-700 rounded-lg p-4 min-h-[100px]"
                                data-category="{{ $category }}" role="list"
                                aria-label="Widget dalam kategori {{ $this->getCategoryDisplayName($category) }}">

                                @if (isset($layout['widgets'][$category]))
                                    @foreach ($layout['widgets'][$category] as $widget)
                                        <div class="sortable-item bg-white dark:bg-gray-600 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-500 mb-2 cursor-move focus:outline-none focus:ring-2 focus:ring-primary-500"
                                            data-widget="{{ $widget['class'] }}" role="listitem" tabindex="0"
                                            aria-label="Widget: {{ $this->getWidgetDisplayName($widget['class']) }}"
                                            @keydown.enter="handleKeyboardMove($event)"
                                            @keydown.space.prevent="handleKeyboardMove($event)">

                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $this->getWidgetDisplayName($widget['class']) }}
                                                </span>

                                                <div class="flex items-center space-x-2">
                                                    <!-- Visibility indicator -->
                                                    @if ($widget['visible'] ?? true)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                            Kelihatan
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300">
                                                            Tersembunyi
                                                        </span>
                                                    @endif

                                                    <!-- Drag handle -->
                                                    <svg class="w-4 h-4 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 8h16M4 16h16" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                        Tiada widget dalam kategori ini
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Reset Button -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showResetConfirmation = true"
                            class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-red-900 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800">
                            Kembalikan ke Tetapan Asal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Visibility Tab -->
            <div x-show="activeTab === 'visibility'" role="tabpanel" id="panel-visibility"
                aria-labelledby="tab-visibility">
                <div class="space-y-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Pilih widget mana yang ingin dipaparkan atau disembunyikan pada dashboard anda.
                    </div>

                    @foreach ($availableWidgets as $widget)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <label for="visibility-{{ $loop->index }}"
                                class="flex-1 text-sm font-medium text-gray-900 dark:text-white cursor-pointer">
                                {{ $this->getWidgetDisplayName($widget['widget_class']) }}
                            </label>

                            <button type="button" id="visibility-{{ $loop->index }}"
                                @click="$wire.toggleWidgetVisibility('{{ $widget['widget_class'] }}')"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="{
                                    'bg-primary-600': {{ $this->isWidgetVisible($widget['widget_class']) ? 'true' : 'false' }},
                                    'bg-gray-200 dark:bg-gray-600': {{ $this->isWidgetVisible($widget['widget_class']) ? 'false' : 'true' }}
                                }"
                                role="switch"
                                :aria-checked="{{ $this->isWidgetVisible($widget['widget_class']) ? 'true' : 'false' }}"
                                aria-label="Toggle keterlihatan untuk {{ $this->getWidgetDisplayName($widget['widget_class']) }}">
                                <span
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="{
                                        'translate-x-5': {{ $this->isWidgetVisible($widget['widget_class']) ? 'true' : 'false' }},
                                        'translate-x-0': {{ $this->isWidgetVisible($widget['widget_class']) ? 'false' : 'true' }}
                                    }"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sizes Tab -->
            <div x-show="activeTab === 'sizes'" role="tabpanel" id="panel-sizes" aria-labelledby="tab-sizes">
                <div class="space-y-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Pilih saiz untuk setiap widget mengikut keutamaan anda.
                    </div>

                    @foreach ($availableWidgets as $widget)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $this->getWidgetDisplayName($widget['widget_class']) }}
                                </span>
                            </div>

                            <div class="flex space-x-2">
                                @foreach ($widgetSizes as $size)
                                    <button type="button"
                                        @click="$wire.updateWidgetSize('{{ $widget['widget_class'] }}', '{{ $size }}')"
                                        class="flex-1 px-3 py-2 text-xs font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                        :class="{
                                            'bg-primary-600 text-white': '{{ $this->getWidgetSize($widget['widget_class']) }}'
                                            === '{{ $size }}',
                                            'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-500': '{{ $this->getWidgetSize($widget['widget_class']) }}'
                                            !== '{{ $size }}'
                                        }"
                                        aria-label="Set saiz {{ $this->getSizeDisplayName($size) }} untuk {{ $this->getWidgetDisplayName($widget['widget_class']) }}">
                                        {{ $this->getSizeDisplayName($size) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Import/Export Tab -->
            <div x-show="activeTab === 'import-export'" role="tabpanel" id="panel-import-export"
                aria-labelledby="tab-import-export">
                <div class="space-y-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Export konfigurasi semasa atau import konfigurasi yang telah disimpan.
                    </div>

                    <!-- Export Section -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900 dark:text-white">Export Konfigurasi</h4>
                        <button type="button" @click="$wire.exportLayout()"
                            class="w-full px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 border border-primary-200 rounded-md hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-primary-900 dark:text-primary-300 dark:border-primary-700 dark:hover:bg-primary-800">
                            Export Susun Atur Semasa
                        </button>
                    </div>

                    <!-- Import Section -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900 dark:text-white">Import Konfigurasi</h4>
                        <button type="button" @click="showImportModal = true"
                            class="w-full px-4 py-2 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-green-900 dark:text-green-300 dark:border-green-700 dark:hover:bg-green-800">
                            Import Susun Atur
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
