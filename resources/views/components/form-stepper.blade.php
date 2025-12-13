@props([
    'steps' => [],
    'currentStep' => 1,
])

<nav aria-label="{{ __('common.form_progress') }}" class="stepper">
    @foreach($steps as $index => $step)
        @php
            $stepNumber = $index + 1;
            $isActive = $stepNumber === $currentStep;
            $isCompleted = $stepNumber < $currentStep;
            $stepClass = $isActive ? 'active' : ($isCompleted ? 'completed' : '');
        @endphp

        <div class="stepper-step {{ $stepClass }}" 
             aria-current="{{ $isActive ? 'step' : 'false' }}">
            <div class="stepper-circle">
                @if($isCompleted)
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @else
                    {{ $stepNumber }}
                @endif
            </div>
            <span class="stepper-label">{{ $step }}</span>
        </div>
    @endforeach
</nav>
