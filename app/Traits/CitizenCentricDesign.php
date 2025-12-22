<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Citizen-Centric Design Trait
 *
 * Implements MyGovEA "Berpaksikan Rakyat" (Citizen-Centric) design principles
 * per Requirement 23.1.
 *
 * Features:
 * - User needs prioritization
 * - Clear feedback mechanisms
 * - Intuitive navigation
 * - Reduced cognitive load
 * - Progress indicators
 * - Contextual help
 *
 * @see Requirements 23.1, 23.5
 *
 * @trace MyGovEA §1, D12 §4
 */
trait CitizenCentricDesign
{
    /**
     * Provide clear, immediate feedback for user actions
     *
     * @param  string  $message  The feedback message
     * @param  string  $type  The feedback type (success, error, info, warning)
     * @param  int  $duration  Duration in milliseconds (0 = persistent)
     */
    protected function provideFeedback(string $message, string $type = 'success', int $duration = 5000): void
    {
        $this->dispatch('user-feedback', [
            'message' => $message,
            'type' => $type,
            'duration' => $duration,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Show progress indicator for multi-step processes
     *
     * @param  int  $currentStep  Current step number
     * @param  int  $totalSteps  Total number of steps
     * @param  string  $stepLabel  Label for current step
     */
    protected function showProgress(int $currentStep, int $totalSteps, string $stepLabel): void
    {
        $this->dispatch('progress-update', [
            'current' => $currentStep,
            'total' => $totalSteps,
            'label' => $stepLabel,
            'percentage' => round(($currentStep / $totalSteps) * 100),
        ]);
    }

    /**
     * Provide contextual help for complex fields
     *
     * @param  string  $fieldName  The field identifier
     * @param  string  $helpText  The help text content
     * @param  string  $helpType  Type of help (tooltip, modal, inline)
     */
    

/**
 * @return array<string, mixed>
 */
protected function provideContextualHelp(string $fieldName, string $helpText, string $helpType = 'tooltip'): array
    {
        return [
            'field' => $fieldName,
            'help' => $helpText,
            'type' => $helpType,
            'icon' => 'heroicon-o-question-mark-circle',
        ];
    }

    /**
     * Reduce cognitive load by chunking information
     *
     * @param  array  $items  Items to chunk
     * @param  int  $chunkSize  Size of each chunk (default: 5-7 items per Miller's Law)
     */
    

/**
 * @return array<string, mixed>
 */
protected function chunkInformation(array $items, int $chunkSize = 7): array
    {
        return array_chunk($items, $chunkSize);
    }

    /**
     * Provide clear navigation cues
     *
     * @param  string  $currentLocation  Current page/section
     * @param  array  $breadcrumbs  Breadcrumb trail
     */
    protected function provideNavigationCues(string $currentLocation, array $breadcrumbs = []): void
    {
        $this->dispatch('navigation-update', [
            'current' => $currentLocation,
            'breadcrumbs' => $breadcrumbs,
            'canGoBack' => count($breadcrumbs) > 1,
        ]);
    }

    /**
     * Validate user input with clear, actionable error messages
     *
     * @param  string  $field  Field name
     * @param  string  $error  Error message
     * @param  string  $suggestion  Suggestion to fix the error
     */
    protected function provideValidationFeedback(string $field, string $error, string $suggestion = ''): void
    {
        $this->dispatch('validation-feedback', [
            'field' => $field,
            'error' => $error,
            'suggestion' => $suggestion,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Show loading state with clear indication
     *
     * @param  string  $action  The action being performed
     * @param  bool  $isLoading  Loading state
     */
    protected function showLoadingState(string $action, bool $isLoading = true): void
    {
        $this->dispatch('loading-state', [
            'action' => $action,
            'isLoading' => $isLoading,
            'message' => $isLoading ? __('common.processing') : __('common.complete'),
        ]);
    }

    /**
     * Provide success confirmation with next steps
     *
     * @param  string  $message  Success message
     * @param  array  $nextSteps  Array of next available actions
     */
    protected function provideSuccessConfirmation(string $message, array $nextSteps = []): void
    {
        $this->dispatch('success-confirmation', [
            'message' => $message,
            'nextSteps' => $nextSteps,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Simplify complex forms by showing only relevant fields
     *
     * @param  array  $allFields  All available fields
     * @param  array  $conditions  Conditions to determine field visibility
     * @return array Filtered fields
     */
    

/**
 * @return array<string, mixed>
 */
protected function showRelevantFields(array $allFields, array $conditions): array
    {
        return array_filter($allFields, function ($field) use ($conditions) {
            if (! isset($field['condition'])) {
                return true;
            }

            return $conditions[$field['condition']] ?? false;
        });
    }

    /**
     * Provide inline validation feedback (real-time)
     *
     * @param  string  $field  Field name
     * @param  mixed  $value  Field value
     * @param  array  $rules  Validation rules
     * @return array Validation result
     */
    

/**
 * @return array<string, mixed>
 */
protected function provideInlineValidation(string $field, mixed $value, array $rules): array
    {
        try {
            $validator = validator([$field => $value], [$field => $rules]);

            if ($validator->fails()) {
                return [
                    'valid' => false,
                    'errors' => $validator->errors()->get($field),
                    'field' => $field,
                ];
            }

            return [
                'valid' => true,
                'field' => $field,
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'errors' => [__('validation.invalid')],
                'field' => $field,
            ];
        }
    }

    /**
     * Track user interaction for continuous improvement
     *
     * @param  string  $action  User action
     * @param  array  $metadata  Additional metadata
     */
    protected function trackUserInteraction(string $action, array $metadata = []): void
    {
        // Log user interaction for analytics and improvement
        logger()->info('User interaction tracked', [
            'action' => $action,
            'metadata' => $metadata,
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toIso8601String(),
        ]);

        // Dispatch event for real-time analytics
        $this->dispatch('user-interaction', [
            'action' => $action,
            'metadata' => $metadata,
        ]);
    }
}
