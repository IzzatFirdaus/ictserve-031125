<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Services\FuzzySearchService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Unified Search Component
 *
 * Provides fuzzy search across tickets and loans with autocomplete.
 *
 * @component UnifiedSearch
 *
 * @description Search across helpdesk tickets and loan applications
 *
 * @author ICTServe Development Team
 *
 * @version 1.0.0
 *
 * @trace D12 §6.14, D13 §3.7
 *
 * @wcag SC 1.3.1 Info and Relationships, SC 2.1.1 Keyboard, SC 4.1.2 Name Role Value
 *
 * @requirements 22.1, 22.2, 22.3, 22.4, 22.5
 */
class UnifiedSearch extends Component
{
    /**
     * Search query
     */
    public string $query = '';

    /**
     * Whether the search dropdown is open
     */
    public bool $isOpen = false;

    /**
     * Include tickets in search
     */
    public bool $includeTickets = true;

    /**
     * Include loans in search
     */
    public bool $includeLoans = true;

    /**
     * Currently selected suggestion index for keyboard navigation
     */
    public int $selectedIndex = -1;

    /**
     * Placeholder text
     */
    public string $placeholder = '';

    /**
     * Whether to show the expanded search modal
     */
    public bool $showModal = false;

    /**
     * Mount the component
     */
    public function mount(
        string $placeholder = '',
        bool $includeTickets = true,
        bool $includeLoans = true
    ): void {
        $this->placeholder = $placeholder ?: __('common.search.placeholder');
        $this->includeTickets = $includeTickets;
        $this->includeLoans = $includeLoans;
    }

    /**
     * Get autocomplete suggestions
     *
     * @return array<int, array{text: string, type: string, count: int}>
     */
    #[Computed]
    public function suggestions(): array
    {
        if (\strlen($this->query) < 2) {
            return [];
        }

        $service = app(FuzzySearchService::class);

        return $service->getAutocompleteSuggestions($this->query, 8);
    }

    /**
     * Get search results
     *
     * @return array{tickets: \Illuminate\Support\Collection<int, \App\Models\HelpdeskTicket>, loans: \Illuminate\Support\Collection<int, \App\Models\LoanApplication>, suggestions: array<int, string>, total: int}
     */
    #[Computed]
    public function results(): array
    {
        if (\strlen($this->query) < 2) {
            return [
                'tickets' => collect(),
                'loans' => collect(),
                'suggestions' => [],
                'total' => 0,
            ];
        }

        $service = app(FuzzySearchService::class);

        return $service->search($this->query, [
            'include_tickets' => $this->includeTickets,
            'include_loans' => $this->includeLoans,
            'limit' => 10,
        ]);
    }

    /**
     * Handle query update
     */
    public function updatedQuery(): void
    {
        $this->isOpen = \strlen($this->query) >= 2;
        $this->selectedIndex = -1;
    }

    /**
     * Select a suggestion
     */
    public function selectSuggestion(string $text): void
    {
        $this->query = $text;
        $this->isOpen = false;
        $this->performSearch();
    }

    /**
     * Perform the search
     */
    public function performSearch(): void
    {
        if (\strlen($this->query) < 2) {
            return;
        }

        $this->isOpen = false;
        $this->showModal = true;
    }

    /**
     * Handle keyboard navigation - move up
     */
    public function moveUp(): void
    {
        $suggestions = $this->suggestions;
        $count = \count($suggestions);

        if ($count === 0) {
            return;
        }

        $this->selectedIndex = $this->selectedIndex <= 0
            ? $count - 1
            : $this->selectedIndex - 1;
    }

    /**
     * Handle keyboard navigation - move down
     */
    public function moveDown(): void
    {
        $suggestions = $this->suggestions;
        $count = \count($suggestions);

        if ($count === 0) {
            return;
        }

        $this->selectedIndex = $this->selectedIndex >= $count - 1
            ? 0
            : $this->selectedIndex + 1;
    }

    /**
     * Handle enter key
     */
    public function handleEnter(): void
    {
        $suggestions = $this->suggestions;

        if ($this->selectedIndex >= 0 && isset($suggestions[$this->selectedIndex])) {
            $this->selectSuggestion($suggestions[$this->selectedIndex]['text']);
        } else {
            $this->performSearch();
        }
    }

    /**
     * Handle escape key
     */
    public function handleEscape(): void
    {
        $this->isOpen = false;
        $this->selectedIndex = -1;
    }

    /**
     * Close the search modal
     */
    #[On('close-search-modal')]
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
     * Open the search dropdown
     */
    public function openDropdown(): void
    {
        if (\strlen($this->query) >= 2) {
            $this->isOpen = true;
        }
    }

    /**
     * Close the search dropdown
     */
    public function closeDropdown(): void
    {
        $this->isOpen = false;
        $this->selectedIndex = -1;
    }

    /**
     * Clear the search
     */
    public function clearSearch(): void
    {
        $this->query = '';
        $this->isOpen = false;
        $this->selectedIndex = -1;
        $this->showModal = false;
    }

    /**
     * Highlight matched text
     */
    public function highlightMatch(string $text): string
    {
        if ($this->query === '') {
            return e($text);
        }

        $service = app(FuzzySearchService::class);

        return $service->highlightMatches(e($text), $this->query);
    }

    /**
     * Get the type icon for a result
     */
    public function getTypeIcon(string $type): string
    {
        return match ($type) {
            'ticket' => 'ticket',
            'loan' => 'clipboard-document-list',
            default => 'document',
        };
    }

    /**
     * Get the type label for a result
     */
    public function getTypeLabel(string $type): string
    {
        return match ($type) {
            'ticket' => __('common.search.type_ticket'),
            'loan' => __('common.search.type_loan'),
            default => __('common.search.type_other'),
        };
    }

    public function render(): \Illuminate\View\View: View
    {
        return view('livewire.components.unified-search');
    }
}
