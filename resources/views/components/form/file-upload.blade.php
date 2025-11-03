{{--
    Component name: File Upload
    Description: WCAG 2.2 AA compliant file upload component with drag-and-drop support, validation, and accessible feedback
    Author: dev-team@motac.gov.my
    Version: 1.0.0
    Last Updated: 2025-11-03
    WCAG Level: AA
    Requirements Traceability: D03-FR-006.1, D03-FR-006.2, D03-FR-006.3, D04 §6.1, D10 §7, D12 §7.2, D12 §9, D14 §9
    Browser Support: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
    Standards Compliance: ISO/IEC 40500 (WCAG 2.2 Level AA), D12 (UI/UX), D14 (Style Guide)
--}}

@props([
    'id' => 'file-upload-' . uniqid(),
    'name' => 'files',
    'label' => __('Choose files'),
    'accept' => '',
    'multiple' => false,
    'maxSize' => '5MB',
    'required' => false,
    'error' => '',
    'helpText' => '',
])

@php
    $multipleAttr = $multiple ? 'multiple' : '';
    $requiredAttr = $required ? 'required' : '';
    $ariaRequired = $required ? 'true' : 'false';
    $ariaInvalid = $error ? 'true' : 'false';
    $describedBy = [];
    if ($helpText) $describedBy[] = $id . '-help';
    if ($error) $describedBy[] = $id . '-error';
    $ariaDescribedBy = !empty($describedBy) ? implode(' ', $describedBy) : null;
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-700" aria-label="{{ __('required') }}">*</span>
            @endif
        </label>
    @endif

    @if($helpText)
        <p id="{{ $id }}-help" class="text-sm text-gray-600 mb-2">{{ $helpText }}</p>
    @endif

    <div
        x-data="{
            isDragging: false,
            files: [],
            handleFiles(fileList) {
                this.files = Array.from(fileList);
                this.$refs.fileInput.files = fileList;
            },
            removeFile(index) {
                const dt = new DataTransfer();
                const input = this.$refs.fileInput;
                const files = Array.from(input.files);
                files.splice(index, 1);
                files.forEach(file => dt.items.add(file));
                input.files = dt.files;
                this.files = files;
            }
        }"
        class="relative"
    >
        <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
            :class="{ 'border-blue-600 bg-blue-50': isDragging, 'border-gray-300': !isDragging }"
            class="border-2 border-dashed rounded-lg p-6 text-center transition-colors duration-200"
        >
            <input
                type="file"
                id="{{ $id }}"
                name="{{ $name }}"
                {{ $multipleAttr }}
                {{ $requiredAttr }}
                accept="{{ $accept }}"
                @change="handleFiles($event.target.files)"
                x-ref="fileInput"
                class="sr-only"
                aria-required="{{ $ariaRequired }}"
                aria-invalid="{{ $ariaInvalid }}"
                @if($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
            >

            <label
                for="{{ $id }}"
                class="cursor-pointer block"
            >
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <div class="mt-4">
                    <span class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 min-h-[44px]">
                        {{ __('Select files') }}
                    </span>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ __('or drag and drop files here') }}
                    </p>
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    @if($accept)
                        {{ __('Accepted file types:') }} {{ $accept }}
                    @endif
                    @if($maxSize)
                        <br>{{ __('Maximum file size:') }} {{ $maxSize }}
                    @endif
                </p>
            </label>
        </div>

        <!-- Selected files list -->
        <template x-if="files.length > 0">
            <div class="mt-4 space-y-2">
                <p class="text-sm font-medium text-gray-700">{{ __('Selected files:') }}</p>
                <ul class="divide-y divide-gray-200 border border-gray-200 rounded-md" role="list">
                    <template x-for="(file, index) in files" :key="index">
                        <li class="flex items-center justify-between p-3 hover:bg-gray-50">
                            <div class="flex items-center flex-1 min-w-0">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-sm text-gray-900 truncate" x-text="file.name"></span>
                                <span class="ml-2 text-xs text-gray-500" x-text="'(' + (file.size / 1024).toFixed(2) + ' KB)'"></span>
                            </div>
                            <button
                                type="button"
                                @click="removeFile(index)"
                                class="ml-4 flex-shrink-0 text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 rounded p-1 min-h-[44px] min-w-[44px] flex items-center justify-center"
                                :aria-label="'{{ __('Remove file') }} ' + file.name"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </template>
    </div>

    @if($error)
        <p id="{{ $id }}-error" class="mt-2 text-sm text-red-700" role="alert">{{ $error }}</p>
    @endif
</div>
