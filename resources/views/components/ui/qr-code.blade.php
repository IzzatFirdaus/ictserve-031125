{{--
    QR Code Display Component

    @trace D12 §6.15 (Print Optimization)
    @requirements 20.5 (QR Code Display)

    Usage:
    <x-ui.qr-code type="ticket" reference="TKT-2024-001234" />
    <x-ui.qr-code type="loan" reference="LOAN-2024-001234" />
    <x-ui.qr-code type="asset" reference="ICT-LAPTOP-001" />
    <x-ui.qr-code url="https://example.com/custom" />

    Props:
    - type: 'ticket', 'loan', 'asset', or null for custom URL
    - reference: The reference number (ticket number, application number, or asset tag)
    - url: Custom URL (used when type is null)
    - size: QR code size in pixels (default: 150)
    - label: Optional label text below QR code
    - showLabel: Whether to show the label (default: true)
    - class: Additional CSS classes
--}}

@props([
'type' => null,
'reference' => null,
'url' => null,
'size' => 150,
'label' => null,
'showLabel' => true,
'class' => '',
])

@php
$qrService = app(\App\Services\QrCodeService::class);

// Generate QR code based on type
$qrDataUri = null;
$defaultLabel = null;

if ($type === 'ticket' && $reference) {
$qrDataUri = $qrService->getTicketQrCodeDataUri($reference, $size);
$defaultLabel = __('helpdesk.scan_for_status');
} elseif ($type === 'loan' && $reference) {
$qrDataUri = $qrService->getLoanQrCodeDataUri($reference, $size);
$defaultLabel = __('loan.scan_for_status');
} elseif ($type === 'asset' && $reference) {
$qrDataUri = $qrService->getAssetQrCodeDataUri($reference, $size);
$defaultLabel = __('asset.scan_for_details');
} elseif ($url) {
$qrDataUri = $qrService->generateDataUri($url, $size);
$defaultLabel = __('common.scan_qr_code');
}

$displayLabel = $label ?? $defaultLabel;
@endphp

@if ($qrDataUri)
<div {{ $attributes->merge(['class' => 'qr-code-container text-center print-visible ' . $class]) }}>
    {{-- QR Code Image --}}
    <div
        class="qr-code-wrapper inline-block p-3 bg-white rounded-m shadow-sm border border-gray-200 dark:border-gray-700">
        <img src="{{ $qrDataUri }}" alt="{{ __('common.qr_code_alt', ['reference' => $reference ?? 'URL']) }}"
            width="{{ $size }}" height="{{ $size }}" class="block mx-auto" loading="lazy">
    </div>

    {{-- Label --}}
    @if ($showLabel && $displayLabel)
    <p class="qr-code-label mt-2 text-xs text-gray-500 dark:text-gray-400">
        {{ $displayLabel }}
    </p>
    @endif

    {{-- Reference Number (for print) --}}
    @if ($reference)
    <p class="qr-code-reference mt-1 text-xs font-mono text-gray-600 dark:text-gray-300 print-only">
        {{ $reference }}
    </p>
    @endif
</div>
@else
<div
    {{ $attributes->merge(['class' => 'qr-code-error text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-m ' . $class]) }}>
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('common.qr_code_unavailable') }}
    </p>
</div>
@endif