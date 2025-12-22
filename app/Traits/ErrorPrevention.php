<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Error Prevention Trait
 *
 * Implements MyGovEA "Pencegahan Ralat" (Error Prevention) design principles
 * per Requirement 23.3.
 *
 * Features:
 * - Confirmation dialogs for destructive actions
 * - Clear undo options
 * - Validation before submission
 * - Warning messages for irreversible actions
 *
 * @see Requirements 23.3
 *
 * @trace MyGovEA §17, D12 §6
 */
trait ErrorPrevention
{
    /**
     * Request confirmation before destructive action
     *
     * @param  string  $action  Action identifier (delete, cancel, reject)
     * @param  string  $title  Confirmation dialog title
     * @param  string  $message  Confirmation message
     * @param  string  $confirmText  Confirm button text
     * @param  string  $cancelText  Cancel button text
     */
    protected function requestConfirmation(
        string $action,
        string $title,
        string $message,
        string $confirmText = '',
        string $cancelText = ''
    ): void {
        $this->dispatch('confirm-action', [
            'action' => $action,
            'title' => $title,
            'message' => $message,
            'confirmText' => $confirmText ?: __('common.confirm'),
            'cancelText' => $cancelText ?: __('common.cancel'),
            'type' => 'warning',
            'icon' => 'heroicon-o-exclamation-triangle',
        ]);
    }

    /**
     * Provide undo option for reversible actions
     *
     * @param  string  $action  Action that was performed
     * @param  mixed  $data  Data needed to undo the action
     * @param  int  $duration  Duration in milliseconds to show undo option
     */
    protected function provideUndoOption(string $action, mixed $data, int $duration = 10000): void
    {
        $this->dispatch('undo-available', [
            'action' => $action,
            'data' => $data,
            'duration' => $duration,
            'message' => __('common.undo_available'),
        ]);
    }

    /**
     * Show warning for irreversible actions
     *
     * @param  string  $action  Action identifier
     * @param  array  $consequences  List of consequences
     */
    

/**
 * @param array<string, mixed> $consequences
 */
protected function warnIrreversibleAction(string $action, array $consequences): void
    {
        $this->dispatch('irreversible-warning', [
            'action' => $action,
            'consequences' => $consequences,
            'icon' => 'heroicon-o-exclamation-circle',
            'type' => 'danger',
        ]);
    }

    /**
     * Validate before allowing destructive action
     *
     * @param  string  $action  Action to validate
     * @param  array  $requirements  Requirements that must be met
     * @return bool Whether action can proceed
     */
    

/**
 * @param array<string, mixed> $requirements
 */
protected function validateBeforeDestruction(string $action, array $requirements): bool
    {
        foreach ($requirements as $requirement => $condition) {
            if (! $condition) {
                $this->dispatch('validation-failed', [
                    'action' => $action,
                    'requirement' => $requirement,
                    'message' => __("validation.{$requirement}_required"),
                ]);

                return false;
            }
        }

        return true;
    }

    /**
     * Confirm deletion with typed confirmation
     *
     * @param  string  $itemName  Name of item to delete
     * @param  string  $confirmationPhrase  Phrase user must type to confirm
     */
    protected function requestTypedConfirmation(string $itemName, string $confirmationPhrase = 'DELETE'): void
    {
        $this->dispatch('typed-confirmation-required', [
            'itemName' => $itemName,
            'confirmationPhrase' => $confirmationPhrase,
            'message' => __('common.type_to_confirm', ['phrase' => $confirmationPhrase]),
            'type' => 'danger',
        ]);
    }

    /**
     * Show consequences before proceeding
     *
     * @param  string  $action  Action being performed
     * @param  array  $impacts  List of impacts
     */
    

/**
 * @param array<string, mixed> $impacts
 */
protected function showActionConsequences(string $action, array $impacts): void
    {
        $this->dispatch('show-consequences', [
            'action' => $action,
            'impacts' => $impacts,
            'title' => __('common.action_consequences'),
        ]);
    }
}
