<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Welcome Tour Component
 *
 * Provides interactive onboarding tour for first-time portal users.
 * Features step-by-step walkthrough with tooltips, progress tracking,
 * and skip functionality.
 *
 * @version 1.0.0
 *
 * @since 2025-11-06
 *
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.1: Welcome tour with step-by-step walkthrough
 * - WCAG 2.2 AA: Keyboard navigation, ARIA labels, focus management
 * - D12 §4: Unified component library integration
 */
class WelcomeTour extends Component
{
    /**
     * Current tour step (0-indexed)
     */
    public int $currentStep = 0;

    /**
     * Total number of tour steps
     */
    public int $totalSteps = 5;

    /**
     * Tour visibility state
     */
    public bool $isVisible = false;

    /**
     * Tour completion status
     */
    public bool $isCompleted = false;

    /**
     * Tour steps configuration
     */
    public array $steps = [];

    /**
     * Mount component and initialize tour
     */
    public function mount(): void
    {
        // Check if user has completed tour
        $user = Auth::user();
        $this->isCompleted = $user->hasCompletedTour ?? false;

        // Show tour only for first-time users
        $this->isVisible = ! $this->isCompleted;

        // Initialize tour steps
        $this->steps = $this->getTourSteps();
        $this->totalSteps = count($this->steps);
    }

    /**
     * Get tour steps configuration
     */
    protected function getTourSteps(): array
    {
        return [
            [
                'title' => __('portal.tour.dashboard.title'),
                'description' => __('portal.tour.dashboard.description'),
                'target' => '#dashboard-statistics',
                'position' => 'bottom',
                'icon' => 'chart-bar',
            ],
            [
                'title' => __('portal.tour.submissions.title'),
                'description' => __('portal.tour.submissions.description'),
                'target' => '#submissions-link',
                'position' => 'right',
                'icon' => 'document-text',
            ],
            [
                'title' => __('portal.tour.quick_actions.title'),
                'description' => __('portal.tour.quick_actions.description'),
                'target' => '#quick-actions',
                'position' => 'left',
                'icon' => 'lightning-bolt',
            ],
            [
                'title' => __('portal.tour.notifications.title'),
                'description' => __('portal.tour.notifications.description'),
                'target' => '#notification-bell',
                'position' => 'bottom',
                'icon' => 'bell',
            ],
            [
                'title' => __('portal.tour.profile.title'),
                'description' => __('portal.tour.profile.description'),
                'target' => '#profile-link',
                'position' => 'bottom',
                'icon' => 'user-circle',
            ],
        ];
    }

    /**
     * Navigate to next step
     */
    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps - 1) {
            $this->currentStep++;
            $this->dispatch('tour-step-changed', step: $this->currentStep);
        } else {
            $this->completeTour();
        }
    }

    /**
     * Navigate to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
            $this->dispatch('tour-step-changed', step: $this->currentStep);
        }
    }

    /**
     * Skip tour
     */
    public function skipTour(): void
    {
        $this->isVisible = false;
        $this->markTourAsCompleted();
        $this->dispatch('tour-skipped');
    }

    /**
     * Complete tour
     */
    public function completeTour(): void
    {
        $this->isVisible = false;
        $this->isCompleted = true;
        $this->markTourAsCompleted();
        $this->dispatch('tour-completed');

        session()->flash('success', __('portal.tour.completed'));
    }

    /**
     * Restart tour
     */
    public function restartTour(): void
    {
        $this->currentStep = 0;
        $this->isVisible = true;
        $this->isCompleted = false;
        $this->dispatch('tour-restarted');
    }

    /**
     * Mark tour as completed in user preferences
     */
    protected function markTourAsCompleted(): void
    {
        $user = Auth::user();
        $user->update(['has_completed_tour' => true]);
    }

    /**
     * Get current step data
     */
    public function getCurrentStepData(): array
    {
        return $this->steps[$this->currentStep] ?? [];
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage(): int
    {
        if ($this->totalSteps === 0) {
            return 0;
        }

        return (int) (($this->currentStep + 1) / $this->totalSteps * 100);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.portal.welcome-tour', [
            'currentStepData' => $this->getCurrentStepData(),
            'progressPercentage' => $this->getProgressPercentage(),
        ]);
    }
}
