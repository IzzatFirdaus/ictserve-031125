<?php declare(strict_types=1); ?>

@props(['url'])

{{-- # trace: .kiro/specs/ictserve-update-v3/tasks.md §49.1 Email Template Branding --}}
{{-- # trace: .kiro/specs/ictserve-update-v3/requirements.md §Requirement 21 --}}

<tr>
    <td class="header" style="text-align: center; padding: 25px 0;">
        {{-- Jata Negara (Malaysian Coat of Arms) --}}
        {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
        <a href="{{ $url }}" style="display: inline-block; text-decoration: none; padding: 8px;">
            <img src="{{ asset('images/jata-negara.svg') }}"
                 alt="{{ __('common.jata_negara') }}"
                 style="height: 60px; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;">
        </a>

        {{-- MOTAC Logo --}}
        {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
        <a href="{{ $url }}" style="display: inline-block; text-decoration: none; padding: 8px;">
            <img src="{{ asset('images/motac-logo.jpeg') }}"
                 alt="{{ __('common.motac_logo') }}"
                 style="height: 50px; display: block; margin-left: auto; margin-right: auto;">
        </a>

        {{-- Ministry Tagline - Using MOTAC Primary Blue per Requirement 22.1 --}}
        <p style="color: #0056b3; font-size: 14px; margin-top: 10px; margin-bottom: 0; font-weight: 500;">
            {{ __('common.motac_tagline') }}
        </p>
    </td>
</tr>
