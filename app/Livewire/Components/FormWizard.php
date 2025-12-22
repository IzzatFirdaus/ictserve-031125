<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

/**
 * FormWizard Component
 *
 * WCAG 2.2 AA compliant multi-step form wizard with progressive disclosure,
 * step validation, and accessible progress indicators.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-003.2 (Multi-step Form Navigation)
 * @trace D03-FR-017.4 (Form Wizard Requirements)
 * @trace D12 §6.10 (Motion Tokens)
 * @trace D13 §3.6 (Progressive Disclosure)
 * @trace D13 §3.6.1 (Step Navigation)
 *
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, SC 2.4.8, SC 3.3.4)
 *
 * @version 1.0.0
 *
 * @created 2025-12-05
 *
 * Requirements:
 * - 3.2: Multi-step form navigation with validation
 * - 17.4: Progress indicator with step completion tracking
 *
 * Usage:
 * <livewire:components.form-wizard
 *     :steps="$steps"
 *     :current-step="1"
 *
 *     @step-changed="handleStepChange"
 * />
 */
class FormWizard extends Component
{
    /**
     * Array of step definitions.
     * Each step should have: id, title, description (optional), icon (optional)
     *
     * @var array<int, array{id: string, title: string, description?: string, icon?: string}>
     */
    public array $steps = [];

    /**
     * Current active step (1-indexed).
     */
    public int $currentStep = 1;

    /**
     * Array of completed step indices.
     *
     * @var array<int>
     */
    public array $completedSteps = [];

    /**
     * Whether to allow navigation to any step (vs sequential only).
     */
    public bool $allowSkip = false;

    /**
     * Whether to show step numbers in the progress indicator.
     */
    public bool $showStepNumbers = true;

    /**
     * Whether the wizard is in a loading/processing state.
     */
    public bool $isProcessing = false;

    /**
     * Validation errors for the current step.
     *
     * @var array<string, string>
     */
    public array $stepErrors = [];

    /**
     * Mount the component with initial configuration.
     *
     * @param  array<int, array{id: string, title: string, description?: string, icon?: string}>  $steps
     */
    

/**
 * @param array<string, mixed> $completedSteps
 */
public function mount(
        array $steps = [],
        int $currentStep = 1,
        array $completedSteps = [],
        bool $allowSkip = false,
        bool $showStepNumbers = true
    ): void {
        $this->steps = $steps;
        $this->currentStep = max(1, min($currentStep, count($steps)));
        $this->completedSteps = $completedSteps;
        $this->allowSkip = $allowSkip;
        $this->showStepNumbers = $showStepNumbers;
    }

    /**
     * Navigate to a specific step.
     */
    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > count($this->steps)) {
            return;
        }

        // Check if navigation is allowed
        if (! $this->canNavigateToStep($step)) {
            return;
        }

        $previousStep = $this->currentStep;
        $this->currentStep = $step;
        $this->stepErrors = [];

        // Dispatch event for parent components
        $this->dispatch('step-changed', [
            'previousStep' => $previousStep,
            'currentStep' => $step,
            'stepId' => $this->steps[$step - 1]['id'] ?? null,
        ]);

        // Announce to screen readers
        $this->dispatch('announce', [
            'message' => __('Step :current of :total: :title', [
                'current' => $step,
                'total' => count($this->steps),
                'title' => $this->steps[$step - 1]['title'] ?? '',
            ]),
        ]);
    }

    /**
     * Navigate to the next step.
     */
    public function nextStep(): void
    {
        if ($this->currentStep < count($this->steps)) {
            // Mark current step as completed
            if (! in_array($this->currentStep, $this->completedSteps)) {
                $this->completedSteps[] = $this->currentStep;
            }

            $this->goToStep($this->currentStep + 1);
        }
    }

    /**
     * Navigate to the previous step.
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->goToStep($this->currentStep - 1);
        }
    }

    /**
     * Mark a step as completed.
     */
    public function completeStep(int $step): void
    {
        if ($step >= 1 && $step <= count($this->steps) && ! in_array($step, $this->completedSteps)) {
            $this->completedSteps[] = $step;
            sort($this->completedSteps);
        }
    }

    /**
     * Mark a step as incomplete.
     */
    public function uncompleteStep(int $step): void
    {
        $this->completedSteps = array_values(array_filter(
            $this->completedSteps,
            fn ($s) => $s !== $step
        ));
    }

    /**
     * Set validation errors for the current step.
     *
     * @param  array<string, string>  $errors
     */
    #[On('wizard-step-errors')]
    

/**
 * @param array<string, mixed> $errors
 */
public function setStepErrors(array $errors): void
    {
        $this->stepErrors = $errors;
    }

    /**
     * Clear validation errors.
     */
    public function clearErrors(): void
    {
        $this->stepErrors = [];
    }

    /**
     * Check if navigation to a specific step is allowed.
     */
    public function canNavigateToStep(int $step): bool
    {
        if ($this->allowSkip) {
            return true;
        }

        // Can always go back
        if ($step < $this->currentStep) {
            return true;
        }

        // Can go to next step if current is completed
        if ($step === $this->currentStep + 1 && in_array($this->currentStep, $this->completedSteps)) {
            return true;
        }

        // Can go to any completed step
        if (in_array($step, $this->completedSteps)) {
            return true;
        }

        // Can stay on current step
        return $step === $this->currentStep;
    }

    /**
     * Check if a step is completed.
     */
    public function isStepCompleted(int $step): bool
    {
        return in_array($step, $this->completedSteps);
    }

    /**
     * Check if a step is the current step.
     */
    public function isCurrentStep(int $step): bool
    {
        return $this->currentStep === $step;
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentage(): int
    {
        if (count($this->steps) === 0) {
            return 0;
        }

        return (int) round((count($this->completedSteps) / count($this->steps)) * 100);
    }

    /**
     * Check if on the first step.
     */
    public function isFirstStep(): bool
    {
        return $this->currentStep === 1;
    }

    /**
     * Check if on the last step.
     */
    public function isLastStep(): bool
    {
        return $this->currentStep === count($this->steps);
    }

    /**
     * Get the current step data.
     *
     * @return array{id: string, title: string, description?: string, icon?: string}|null
     */
    public function getCurrentStepData(): ?array
    {
        return $this->steps[$this->currentStep - 1] ?? null;
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.components.form-wizard', [
            'totalSteps' => count($this->steps),
            'progressPercentage' => $this->getProgressPercentage(),
            'currentStepData' => $this->getCurrentStepData(),
        ]);
    }
}
