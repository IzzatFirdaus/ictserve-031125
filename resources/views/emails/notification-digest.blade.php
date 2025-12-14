{{--
/**
 * Notification Digest Email Template
 * 
 * @component Email Template
 * @description WCAG 2.2 AA compliant notification digest email in Bahasa Melayu exclusively
 * @author Pasukan BPM MOTAC
 * @trace D15 Language Standards (Bahasa Melayu sahaja v3.6.0)
 * @version 2.0.0
 * @created 2025-12-14
 */
--}}
@extends('emails.layout-branded', ['subject' => __('Ringkasan Pemberitahuan Anda'), 'isoReference' => 'PK.(S).MOTAC.07.(N1)'])

@section('content')
    {{-- Greeting (Bahasa Melayu sahaja) --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} {{ $user->name }},
    </h2>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 0 0 24px 0;">
        Berikut adalah ringkasan pemberitahuan anda untuk {{ now()->translatedFormat('l, d F Y') }}:
    </p>

    {{-- Notifications List --}}
    @foreach ($notifications as $notification)
        <div
            style="padding: 16px; margin: 16px 0; border-left: 4px solid #0056B3; background-color: #F8F9FA; border-radius: 0 6px 6px 0;">
            <div style="font-weight: 600; margin-bottom: 8px; color: #1F2937;">
                {{ $notification->data['title'] ?? __('Pemberitahuan') }}
            </div>
            <div style="color: #4B5563; margin-bottom: 8px;">
                {{ $notification->data['message'] ?? ($notification->data['body'] ?? __('Tiada kandungan mesej.')) }}
            </div>
            <div style="font-size: 12px; color: #6B7280;">
                {{ $notification->created_at->diffForHumans() }}
            </div>
        </div>
    @endforeach

    {{-- Summary --}}
    <div class="info-box"
        style="background-color: #EFF6FF; border-left: 4px solid #0056B3; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0; color: #1E40AF; font-weight: 600;">
            Anda telah menerima sejumlah <strong>{{ $notifications->count() }}</strong>
            {{ \Illuminate\Support\Str::plural('pemberitahuan', $notifications->count()) }}.
        </p>
    </div>

    {{-- Settings Note --}}
    <p style="color: #4B5563; margin: 24px 0;">
        Anda boleh menguruskan keutamaan pemberitahuan anda dalam tetapan profil.
    </p>

    {{-- Closing --}}
    <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 32px 0;">

    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('common.email_templates.thank_you_ms') }}
    </p>

    <p style="color: #374151; margin: 0;">
        {{ __('common.email_templates.regards_ms') }}<br>
        <strong>{{ __('common.email_templates.bpm_team_ms') }}</strong>
    </p>
@endsection
