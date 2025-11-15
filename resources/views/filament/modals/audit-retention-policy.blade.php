<div class="space-y-4">
    <h2 class="text-lg font-semibold">{{ __('audit.retention.title') }}</h2>

    <p>
        {{ __('audit.retention.description') }}
    </p>

    <p>
        {!! __('audit.retention.docs', [
            'requirements' => '<a class="text-primary-600" href="/docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md" target="_blank">'.__('audit.retention.docs_requirements').'</a>',
            'design' => '<a class="text-primary-600" href="/docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md" target="_blank">'.__('audit.retention.docs_design').'</a>',
        ]) !!}
    </p>

    <p class="text-sm text-muted">
        {{ __('audit.retention.note') }}
    </p>
</div>
