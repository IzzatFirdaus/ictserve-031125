{{--
    reCAPTCHA Enterprise Invisible Component

    Usage:
    <x-recaptcha action="helpdesk_submit" />

    @see Requirements 14.2 - Invisible reCAPTCHA on all guest forms
--}}

@props(['action' => 'submit'])

@php
    $siteKey = config('recaptcha.site_key');
    $enabled = config('recaptcha.enabled', true);
@endphp

@if ($enabled && $siteKey)
    {{-- Hidden input for the reCAPTCHA token --}}
    <input type="hidden" name="recaptcha_token" id="recaptcha_token_{{ $action }}" />

    @once
        {{-- Load reCAPTCHA Enterprise script once --}}
        <script src="https://www.google.com/recaptcha/enterprise.js?render={{ $siteKey }}" async defer></script>
    @endonce

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for reCAPTCHA to load
            if (typeof grecaptcha === 'undefined') {
                console.warn('reCAPTCHA not loaded yet, waiting...');
                setTimeout(arguments.callee, 100);
                return;
            }

            grecaptcha.enterprise.ready(function() {
                // Find the form containing this component
                const tokenInput = document.getElementById('recaptcha_token_{{ $action }}');
                if (!tokenInput) return;

                const form = tokenInput.closest('form');
                if (!form) return;

                // Execute reCAPTCHA on form submit
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    grecaptcha.enterprise.execute('{{ $siteKey }}', {
                        action: '{{ config("recaptcha.actions.$action", $action) }}'
                    }).then(function(token) {
                        tokenInput.value = token;
                        form.submit();
                    }).catch(function(error) {
                        console.error('reCAPTCHA error:', error);
                        // Allow form submission even if reCAPTCHA fails (server will handle)
                        form.submit();
                    });
                });
            });
        });
    </script>
@endif
