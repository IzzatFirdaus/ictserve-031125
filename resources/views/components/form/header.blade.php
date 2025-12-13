@props([
    'title' => '',
    'subtitle' => '',
])

{{--
    FormHeader Component

    Purpose: Branded header for guest forms (helpdesk, loan application)
    Usage: Top of all guest submission forms

    Requirements:
    - 21.3: Display BPM logo from public/images/bpm-logo.png in form headers
    - 22.1: Use official MOTAC color palette with Primary Blue (#0056b3) as dominant brand color

    Reference: D12_UI_UX_DESIGN_GUIDE.md §2, D14_UI_UX_STYLE_GUIDE.md §1
--}}

<div
    class="bg-linear-to-r text-white p-6 rounded-t-lg"
    style="background: linear-gradient(to right, #0056b3, #003d82);"
    role="banner"
    aria-label="{{ __('common.form_header') }}"
>
    <div class="flex items-center space-x-4">
        {{-- BPM Logo (64x64) per Requirement 21.3 --}}
        {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
        <img
            src="{{ asset('images/bpm-logo.png') }}"
            alt="{{ __('common.bpm_logo') }}"
            class="h-16 w-16 rounded object-cover shrink-0 p-1"
            width="64"
            height="64"
            loading="eager"
        >

        <div class="min-w-0 flex-1">
            {{-- Form Title --}}
            <h1 class="text-xl font-bold text-white truncate">
                {{ $title }}
            </h1>

            {{-- Form Subtitle --}}
            @if($subtitle)
                <p class="text-sm text-blue-200 mt-1 truncate">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>
</div>
