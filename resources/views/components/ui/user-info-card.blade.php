{{--
    User Info Card Component (x-ui.user-info-card)

    Displays verified user information with green/teal styling to distinguish
    system-provided data from user-editable form fields.

    @props
    - user: User model or object with name, grade, department properties
    - title: Optional custom title (defaults to 'Verified User Information')
    - showEmail: Boolean to show email field (default: false)
    - showPhone: Boolean to show phone field (default: false)

    @usage
    <x-ui.user-info-card :user="auth()->user()" />
    <x-ui.user-info-card :user="$applicant" :showEmail="true" />

    @trace SRS-FR-001.2; D04 §3.1; Task 2.2.14
    @see design.md Portal-Specific Components
--}}

@props([
'user' => null,
'title' => null,
'showEmail' => false,
'showPhone' => false,
])

@php
$displayUser = $user ?? auth()->user();
$defaultTitle = __('portal.verified_user_info');
@endphp

@if($displayUser)
<div
    {{ $attributes->merge([
        'class' => 'bg-teal-50 dark:bg-teal-900/20 border-l-4 border-teal-500 dark:border-teal-400 p-4 rounded-(--radius-l)',
        'role' => 'region',
        'aria-labelledby' => 'user-info-heading-' . ($displayUser->id ?? 'guest'),
    ]) }}>
    <div class="flex items-start">
        {{-- Info Icon --}}
        <div class="shrink-0" aria-hidden="true">
            <svg class="h-5 w-5 text-teal-500 dark:text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
        </div>

        {{-- User Information --}}
        <div class="ml-3 flex-1">
            <p
                id="user-info-heading-{{ $displayUser->id ?? 'guest' }}"
                class="text-sm font-medium text-teal-800 dark:text-teal-200">
                {{ $title ?? $defaultTitle }}
            </p>

            <dl class="mt-2 text-sm text-teal-700 dark:text-teal-300 space-y-1">
                {{-- Name --}}
                <div class="flex gap-1">
                    <dt class="font-semibold">{{ __('portal.name') }}:</dt>
                    <dd>{{ $displayUser->name ?? __('portal.not_available') }}</dd>
                </div>

                {{-- Grade --}}
                @if($displayUser->grade ?? null)
                <div class="flex gap-1">
                    <dt class="font-semibold">{{ __('portal.grade') }}:</dt>
                    <dd>{{ $displayUser->grade }}</dd>
                </div>
                @endif

                {{-- Department/Division --}}
                @if($displayUser->department ?? $displayUser->division?->name ?? null)
                <div class="flex gap-1">
                    <dt class="font-semibold">{{ __('portal.department') }}:</dt>
                    <dd>{{ $displayUser->department ?? $displayUser->division?->name }}</dd>
                </div>
                @endif

                {{-- Position (if available) --}}
                @if($displayUser->position ?? null)
                <div class="flex gap-1">
                    <dt class="font-semibold">{{ __('portal.position') }}:</dt>
                    <dd>{{ $displayUser->position }}</dd>
                </div>
                @endif

                {{-- Staff ID (if available) --}}
                @if($displayUser->staff_id ?? $displayUser->motac_email ?? null)
                <div class="flex gap-1">
                    <dt class="font-semibold">{{ __('portal.staff_id') }}:</dt>
                    <dd>{{ $displayUser->staff_id ?? $displayUser->motac_email }}</dd>
                </div>
                @endif

                {{-- Email (optional) --}}
                @if($showEmail && ($displayUser->email ?? null))
                <div class="flex gap-1">
                    <dt class="font-semibold">{{ __('portal.email') }}:</dt>
                    <dd>{{ $displayUser->email }}</dd>
                </div>
                @endif

                {{-- Phone (optional) --}}
                @if($showPhone && ($displayUser->phone ?? null))
                <div class="flex gap-1">
                    <dt class="font-semibold">{{ __('portal.phone') }}:</dt>
                    <dd>{{ $displayUser->phone }}</dd>
                </div>
                @endif
            </dl>

            {{-- Optional slot for additional content --}}
            @if($slot->isNotEmpty())
            <div class="mt-3 pt-3 border-t border-teal-200 dark:border-teal-700">
                {{ $slot }}
            </div>
            @endif
        </div>
    </div>
</div>
@endif
