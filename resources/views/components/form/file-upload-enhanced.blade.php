{{--
/**
 * Component: Enhanced File Upload
 * Description: WCAG 2.2 AA compliant file upload with drag-and-drop, progress indicator, and validation
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-017.3 (File Upload Requirements)
 * @trace D12 §6.4 (Loading States)
 * @trace D13 §3.7 (Form Components)
 * @trace D13 §3.7.2 (File Validation)
 * @trace D14 §10.3 (Form Accessibility)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, SC 2.1.1, SC 4.1.2)
 * @version 1.0.0
 * @created 2025-12-05
 *
 * Requirements:
 * - 17.3: Drag-and-drop file upload with Alpine.js
 * - Progress indicator with aria-busy="true"
 * - File type/size validation with clear error messages
 *
 * Usage:
 * <x-form.file-upload-enhanced
 *     wire:model="attachments"
 *     label="Upload Documents"
 *     accept=".pdf,.doc,.docx"
 *     :maxSize="5"
 *     multiple
 * />
 */
--}}

@props([
    'id' => null,
    'name' => 'files',
    'label' => null,
    'accept' => '',
    'multiple' => false,
    'maxSize' => 5,
    'maxFiles' => 10,
    'required' => false,
    'error' => null,
    'helper' => null,
    'disabled' => false,
])

@php
    $id = $id ?? 'file-upload-' . md5(serialize($attributes->getAttributes()));
    $wireModel = $attributes->get('wire:model');
    $hasError =
        $error ||
        ($wireModel &&
            isset($errors) &&
            is_object($errors) &&
            method_exists($errors, 'has') &&
            $errors->has($wireModel));
    $errorMessage =
        $error ??
        ($wireModel && isset($errors) && is_object($errors) && method_exists($errors, 'first')
            ? $errors->first($wireModel)
            : null);

    $describedBy = [];
    if ($helper) {
        $describedBy[] = $id . '-helper';
    }
    if ($hasError) {
        $describedBy[] = $id . '-error';
    }
    $ariaDescribedBy = !empty($describedBy) ? implode(' ', $describedBy) : null;

    $acceptTypes = $accept ? explode(',', $accept) : [];
    $acceptDisplay = implode(', ', array_map(fn($t) => strtoupper(trim($t, '.')), $acceptTypes));
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }} x-data="{
    isDragging: false,
    files: [],
    uploading: false,
    progress: 0,
    errors: [],
    maxSize: {{ $maxSize * 1024 * 1024 }},
    maxFiles: {{ $maxFiles }},
    acceptTypes: {{ json_encode($acceptTypes) }},

    init() {
        // Listen for Livewire upload progress
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('upload:start', () => {
                this.uploading = true;
                this.progress = 0;
            });
            Livewire.hook('upload:progress', (progress) => {
                this.progress = progress;
            });
            Livewire.hook('upload:finish', () => {
                this.uploading = false;
                this.progress = 100;
            });
            Livewire.hook('upload:error', () => {
                this.uploading = false;
                this.progress = 0;
            });
        }
    },

    validateFile(file) {
        const errors = [];

        // Check file size
        if (file.size > this.maxSize) {
            errors.push(`${file.name}: {{ __('File exceeds maximum size of') }} {{ $maxSize }}MB`);
        }

        // Check file type
        if (this.acceptTypes.length > 0) {
            const ext = '.' + file.name.split('.').pop().toLowerCase();
            const mimeType = file.type;
            const isValidType = this.acceptTypes.some(type => {
                type = type.trim().toLowerCase();
                return type === ext || type === mimeType || (type.endsWith('/*') && mimeType.startsWith(type.slice(0, -1)));
            });
            if (!isValidType) {
                errors.push(`${file.name}: {{ __('Invalid file type') }}`);
            }
        }

        return errors;
    },

    handleFiles(fileList) {
        this.errors = [];
        const newFiles = Array.from(fileList);

        // Check max files limit
        if (this.files.length + newFiles.length > this.maxFiles) {
            this.errors.push(`{{ __('Maximum') }} ${this.maxFiles} {{ __('files allowed') }}`);
            return;
        }

        // Validate each file
        newFiles.forEach(file => {
            const fileErrors = this.validateFile(file);
            if (fileErrors.length > 0) {
                this.errors.push(...fileErrors);
            } else {
                this.files.push(file);
            }
        });

        // Update the file input
        this.updateFileInput();

        // Announce to screen readers
        this.announceFiles();
    },

    removeFile(index) {
        this.files.splice(index, 1);
        this.updateFileInput();
        this.announceFiles();
    },

    updateFileInput() {
        const dt = new DataTransfer();
        this.files.forEach(file => dt.items.add(file));
        this.$refs.fileInput.files = dt.files;

        // Trigger change event for Livewire
        this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    },

    announceFiles() {
        const announcement = this.files.length === 0 ?
            '{{ __('No files selected') }}' :
            `${this.files.length} {{ __('file(s) selected') }}`;
        this.$refs.announcement.textContent = announcement;
    },

    formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    },

    clearAll() {
        this.files = [];
        this.errors = [];
        this.updateFileInput();
        this.announceFiles();
    }
}">
    {{-- Label --}}
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if ($required)
                <abbr title="{{ __('Required field') }}" class="text-danger-500 no-underline ml-0.5"
                    aria-label="{{ __('required') }}">*</abbr>
            @endif
        </label>
    @endif

    {{-- Helper text --}}
    @if ($helper)
        <p id="{{ $id }}-helper" class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $helper }}</p>
    @endif

    {{-- Screen reader announcement --}}
    <div x-ref="announcement" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

    {{-- Drop zone --}}
    <div @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
        :class="{
            'border-primary-500 bg-primary-50 dark:bg-primary-900/20': isDragging,
            'border-danger-500 bg-danger-50 dark:bg-danger-900/20': {{ $hasError ? 'true' : 'false' }},
            'border-gray-300 dark:border-gray-600': !isDragging && !{{ $hasError ? 'true' : 'false' }}
        }"
        class="relative border-2 border-dashed rounded-lg p-6 text-center transition-all duration-200"
        :aria-busy="uploading">
        {{-- Hidden file input --}}
        <input type="file" id="{{ $id }}" name="{{ $name }}" x-ref="fileInput"
            @if ($multiple) multiple @endif
            @if ($required) required aria-required="true" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
            @if ($accept) accept="{{ $accept }}" @endif
            @if ($disabled) disabled @endif @change="handleFiles($event.target.files)" class="sr-only"
            {{ $attributes->except(['class', 'wire:model']) }}>

        {{-- Upload icon --}}
        <div class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
            </svg>
        </div>

        {{-- Upload prompt --}}
        <div class="mt-4">
            <label for="{{ $id }}"
                class="inline-flex items-center justify-center px-4 py-2 min-h-11 min-w-11 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus-within:ring-3 focus-within:ring-primary-500 focus-within:ring-offset-2 cursor-pointer transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('Select files') }}
            </label>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('or drag and drop') }}
            </p>
        </div>

        {{-- File constraints info --}}
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            @if ($acceptDisplay)
                {{ __('Accepted:') }} {{ $acceptDisplay }}
                <span class="mx-1">•</span>
            @endif
            {{ __('Max size:') }} {{ $maxSize }}MB
            @if ($multiple)
                <span class="mx-1">•</span>
                {{ __('Max files:') }} {{ $maxFiles }}
            @endif
        </p>

        {{-- Progress indicator --}}
        <div x-show="uploading" x-transition class="mt-4" role="progressbar" :aria-valuenow="progress"
            aria-valuemin="0" aria-valuemax="100" :aria-label="'{{ __('Upload progress') }}: ' + progress + '%'">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                <div class="bg-primary-500 h-2 rounded-full transition-all duration-300"
                    :style="'width: ' + progress + '%'"></div>
            </div>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                x-text="'{{ __('Uploading...') }} ' + progress + '%'"></p>
        </div>
    </div>

    {{-- Validation errors from Alpine --}}
    <template x-if="errors.length > 0">
        <div class="mt-2 space-y-1" role="alert" aria-live="assertive">
            <template x-for="(error, index) in errors" :key="index">
                <p class="text-sm text-danger-600 dark:text-danger-400 flex items-center gap-1">
                    <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span x-text="error"></span>
                </p>
            </template>
        </div>
    </template>

    {{-- Server-side error --}}
    @if ($hasError && $errorMessage)
        <p id="{{ $id }}-error"
            class="mt-2 text-sm text-danger-600 dark:text-danger-400 flex items-center gap-1" role="alert">
            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <span>{{ $errorMessage }}</span>
        </p>
    @endif

    {{-- Selected files list --}}
    <template x-if="files.length > 0">
        <div class="mt-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Selected files') }} (<span x-text="files.length"></span>)
                </p>
                <button type="button" @click="clearAll()"
                    class="text-sm text-danger-600 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-300 focus:outline-none focus:underline">
                    {{ __('Clear all') }}
                </button>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden"
                role="list">
                <template x-for="(file, index) in files" :key="index">
                    <li
                        class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-center min-w-0 gap-3">
                            <svg class="h-5 w-5 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-900 dark:text-gray-100 truncate" x-text="file.name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatSize(file.size)">
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="removeFile(index)"
                            class="ml-4 p-2 min-h-11 min-w-11 flex items-center justify-center text-gray-400 hover:text-danger-600 dark:hover:text-danger-400 focus:outline-none focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2 rounded-lg transition-colors"
                            :aria-label="'{{ __('Remove') }} ' + file.name">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </li>
                </template>
            </ul>
        </div>
    </template>
</div>
