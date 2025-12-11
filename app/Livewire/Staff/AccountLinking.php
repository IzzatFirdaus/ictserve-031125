<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Contracts\AccountLinkingServiceInterface;
use App\Models\User;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Account Linking Component for ICTServe v3.5.0
 *
 * Allows authenticated staff to link their historical guest submissions
 * (helpdesk tickets and loan applications) to their new account.
 *
 * Features:
 * - Email input for finding unlinked submissions
 * - Display matching submissions for confirmation
 * - Link button with success feedback
 * - WCAG 2.2 AA compliant bilingual interface
 * - Audit trail logging for compliance
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D02 FR-050 Optional account linking
 * @see D03 SRS-DATA-001 Hybrid data association
 * @see D09 §4.6 Audit trail requirements
 * @see Requirements 18.1, 18.2, 18.3, 18.4, 18.5
 *
 * @requirements 18.1, 18.2, 18.3, 18.4, 18.5
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-12-03
 */
class AccountLinking extends Component
{
    use OptimizedLivewireComponent;

    /**
     * Email address to search for unlinked submissions
     */
    #[Validate('required|email')]
    public string $searchEmail = '';

    /**
     * Whether a search has been performed
     */
    public bool $hasSearched = false;

    /**
     * Selected submission IDs for linking
     *
     * @var array<int, array{type: string, id: int}>
     */
    public array $selectedSubmissions = [];

    /**
     * Whether linking is in progress
     */
    public bool $isLinking = false;

    /**
     * Success message after linking
     */
    public string $successMessage = '';

    /**
     * Error message if linking fails
     */
    public string $errorMessage = '';

    /**
     * Found unlinked submissions
     *
     * @var Collection<int, array{type: string, id: int, reference: string, created_at: string, subject: string|null, status: string}>
     */
    public Collection $foundSubmissions;

    /**
     * Account linking service instance
     */
    protected AccountLinkingServiceInterface $linkingService;

    /**
     * Boot the component
     */
    public function boot(AccountLinkingServiceInterface $linkingService): void
    {
        $this->linkingService = $linkingService;
    }

    /**
     * Mount the component
     */
    public function mount(): void
    {
        $this->foundSubmissions = collect();

        // Pre-fill with user's email for convenience
        $user = $this->getUser();
        $this->searchEmail = $user->email;
    }

    /**
     * Get authenticated user
     */
    protected function getUser(): User
    {
        $user = Auth::user();
        assert($user instanceof User);

        return $user;
    }

    /**
     * Get linking statistics for the current user
     *
     * @return array{linked_tickets: int, linked_loans: int, unlinked_tickets: int, unlinked_loans: int}
     */
    #[Computed]
    public function linkingStatistics(): array
    {
        return $this->linkingService->getLinkingStatistics($this->getUser());
    }

    /**
     * Check if user has any unlinked submissions
     */
    #[Computed]
    public function hasUnlinkedSubmissions(): bool
    {
        return $this->linkingService->hasUnlinkedSubmissions($this->getUser());
    }

    /**
     * Get count of linked submissions
     */
    #[Computed]
    public function linkedCount(): int
    {
        return $this->linkingService->getLinkedSubmissionCount($this->getUser());
    }

    /**
     * Search for unlinked submissions by email
     */
    public function searchSubmissions(): void
    {
        $this->validate();

        $this->clearMessages();
        $this->hasSearched = true;
        $this->selectedSubmissions = [];

        $this->foundSubmissions = $this->linkingService->findUnlinkedSubmissions($this->searchEmail);

        if ($this->foundSubmissions->isEmpty()) {
            $this->errorMessage = __('account_linking.no_submissions_found');
        }
    }

    /**
     * Toggle selection of a submission
     *
     * @param  string  $type  The submission type (ticket or loan)
     * @param  int  $id  The submission ID
     */
    public function toggleSelection(string $type, int $id): void
    {
        $key = $this->findSelectionKey($type, $id);

        if ($key !== null) {
            unset($this->selectedSubmissions[$key]);
            $this->selectedSubmissions = array_values($this->selectedSubmissions);
        } else {
            $this->selectedSubmissions[] = ['type' => $type, 'id' => $id];
        }
    }

    /**
     * Check if a submission is selected
     *
     * @param  string  $type  The submission type
     * @param  int  $id  The submission ID
     */
    public function isSelected(string $type, int $id): bool
    {
        return $this->findSelectionKey($type, $id) !== null;
    }

    /**
     * Find the key of a selected submission
     *
     * @param  string  $type  The submission type
     * @param  int  $id  The submission ID
     * @return int|null The array key or null if not found
     */
    protected function findSelectionKey(string $type, int $id): ?int
    {
        foreach ($this->selectedSubmissions as $key => $submission) {
            if ($submission['type'] === $type && $submission['id'] === $id) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Select all found submissions
     */
    public function selectAll(): void
    {
        $this->selectedSubmissions = $this->foundSubmissions->map(fn (array $submission): array => [
            'type' => $submission['type'],
            'id' => $submission['id'],
        ])->toArray();
    }

    /**
     * Deselect all submissions
     */
    public function deselectAll(): void
    {
        $this->selectedSubmissions = [];
    }

    /**
     * Link selected submissions to the user's account
     */
    public function linkSubmissions(): void
    {
        if (empty($this->selectedSubmissions)) {
            $this->errorMessage = __('account_linking.no_submissions_selected');

            return;
        }

        $this->isLinking = true;
        $this->clearMessages();

        try {
            $linkedCount = $this->linkingService->linkSubmissions(
                $this->getUser(),
                $this->selectedSubmissions
            );

            if ($linkedCount > 0) {
                $this->successMessage = trans_choice('account_linking.submissions_linked_success', $linkedCount, ['count' => $linkedCount]);

                // Reset the form
                $this->foundSubmissions = collect();
                $this->selectedSubmissions = [];
                $this->hasSearched = false;

                // Dispatch success notification
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => $this->successMessage,
                ]);
            } else {
                $this->errorMessage = __('account_linking.linking_failed');
            }
        } catch (\Exception $e) {
            $this->errorMessage = __('account_linking.linking_error');

            report($e);
        } finally {
            $this->isLinking = false;
        }
    }

    /**
     * Clear all messages
     */
    protected function clearMessages(): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    /**
     * Reset the search form
     */
    public function resetSearch(): void
    {
        $this->searchEmail = $this->getUser()->email;
        $this->foundSubmissions = collect();
        $this->selectedSubmissions = [];
        $this->hasSearched = false;
        $this->clearMessages();
    }

    /**
     * Get the count of selected submissions
     */
    #[Computed]
    public function selectedCount(): int
    {
        return count($this->selectedSubmissions);
    }

    /**
     * Check if all found submissions are selected
     */
    #[Computed]
    public function allSelected(): bool
    {
        if ($this->foundSubmissions->isEmpty()) {
            return false;
        }

        return count($this->selectedSubmissions) === $this->foundSubmissions->count();
    }

    /**
     * Render the component
     */
    public function render(): \Illuminate\View\View: View
    {
        $view = view('livewire.staff.account-linking');
        assert($view instanceof View);

        return $view->layout('layouts.portal');
    }

    /**
     * Get placeholder view for lazy loading
     */
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="animate-pulse">
                <div class="h-8 bg-gray-200 rounded w-1/3 mb-6"></div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-32"></div>
                </div>
            </div>
        </div>
        HTML;
    }
}
