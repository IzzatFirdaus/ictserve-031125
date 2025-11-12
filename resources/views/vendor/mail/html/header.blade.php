<?php declare(strict_types=1); ?>

@props(['url'])

{{-- # trace: .kiro/specs/frontend-pages-redesign/design.md §Email Branding --}}

@php
    $brandSlot = trim((string) ($slot ?? ''));
@endphp

<tr>
    <td class="header" style="padding: 0 24px 24px;">
        <a href="{{ $url }}" style="display: inline-flex; align-items: center; gap: 12px;">
            @if ($brandSlot === '' || $brandSlot === 'Laravel')
                <img src="{{ asset('images/motac-logo.png') }}" alt="{{ __('common.motac_logo') }}" width="120"
                    height="120" class="logo" loading="lazy" decoding="async">
                <span style="font-size: 18px; font-weight: 600; color: #0f172a;">
                    {{ config('app.name', 'ICTServe') }}
                </span>
            @else
                {!! $slot !!}
            @endif
        </a>
    </td>
</tr>
