{{--
    Partial: Flash Messages
    Description: Displays session flash messages using MyDS alert styling.
    Author: Pasukan BPM MOTAC
    Trace: D12 §4, D14 §9.3
    Version: 1.0.0
    Updated: 2025-12-20
--}}

@php
    $messages = [
        'success' => session('success'),
        'info' => session('info'),
        'warning' => session('warning'),
        'danger' => session('error') ?? session('danger'),
    ];
@endphp

@foreach ($messages as $variant => $message)
    @if ($message)
        <x-ui.alert :variant="$variant">
            {{ $message }}
        </x-ui.alert>
    @endif
@endforeach
