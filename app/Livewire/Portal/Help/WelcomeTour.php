<?php

declare(strict_types=1);

namespace App\Livewire\Portal\Help;

use Livewire\Attributes\On;
use Livewire\Component;

class WelcomeTour extends Component
{
    public int $currentStep = 1;

    public int $totalSteps = 6;

    public bool $isVisible = false;

    /**
     * Tour steps configuration
     */
    protected array $steps = [
        1 => [
            'target' => '#dashboard-statistics',
            'title' => 'portal.help.tour.dashboard.title',
            'description' => 'portal.help.tour.dashboard.description',
            'position' => 'bottom',
        ],
        2 => [
            'target' => '#quick-actions',
            'title' => 'portal.help.tour.quick_actions.title',
            'description' => 'portal.help.tour.quick_actions.description',
            'position' => 'bottom',
        ],
        3 => [
            'target' => '#submissions-link',
            'title' => 'portal.help.tour.submissions.title',
            'description' => 'portal.help.tour.submissions.description',
            'position' => 'right',
        ],
        4 => [
            'target' => '#profile-link',
            'title' => 'portal.help.tour.profile.title',
            'description' => 'portal.help.tour.profile.description',
            'position' => 'right',
        ],
        5 => [
            'target' => '#notifications-bell',
            'title' => 'portal.help.tour.notifications.title',
            'description' => 'portal.help.tour.notifications.description',
            'position' => 'left',
        ],
        6 => [
            'target' => '#help-icon',
            'title' => 'portal.help.tour.help.title',
            'description' => 'portal.help.tour.help.description',
            'position' => 'left',
        ],
    ];

    /**
     * Mount the component
     */
    public function mount(): void
    {
        // Check if user has completed tour
        $hasCompletedTour = auth()->user()?->preferences()
            ->where('key', 'has_completed_welcome_tour')
            ->where('value', 'true')
            ->exists() ?? false;

        $this->isVisible = ! $hasCompletedTour;
    }

    /**
     * Get current step data
     */
    public function getCurrentStepData(): array
    {
        return $this->steps[$this->currentStep] ?? [];
    }

    /**
     * Move to next step
     */
    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        } else {
            $this->completeTour();
        }
    }

    /**
     * Move to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Go to specific step
     */
    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
        }
    }

    /**
     * Skip tour
     */
    public function skipTour(): void
    {
        $this->isVisible = false;
        $this->dispatch('tour-skipped');
    }

    /**
     * Complete tour
     */
    public function completeTour(): void
    {
        // Save tour completion preference
        auth()->user()?->preferences()->updateOrCreate(
            ['key' => 'has_completed_welcome_tour'],
            ['value' => 'true']
        );

        $this->isVisible = false;
        $this->dispatch('tour-completed');
    }

    /**
     * Restart tour
     */
    #[On('restart-tour')]
    public function restartTour(): void
    {
        $this->currentStep = 1;
        $this->isVisible = true;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage(): int
    {
        return (int) (($this->currentStep / $this->totalSteps) * 100);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.portal.help.welcome-tour', [
            'stepData' => $this->getCurrentStepData(),
            'progressPercentage' => $this->getProgressPercentage(),
        ]);
    }
}
