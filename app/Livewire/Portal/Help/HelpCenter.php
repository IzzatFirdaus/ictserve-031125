<?php

declare(strict_types=1);

namespace App\Livewire\Portal\Help;

use Livewire\Attributes\Computed;
use Livewire\Component;

class HelpCenter extends Component
{
    public string $search = '';

    public string $selectedCategory = 'all';

    /**
     * Help article categories
     */
    protected array $categories = [
        'getting_started' => 'portal.help.categories.getting_started',
        'helpdesk' => 'portal.help.categories.helpdesk',
        'loans' => 'portal.help.categories.loans',
        'profile' => 'portal.help.categories.profile',
        'approvals' => 'portal.help.categories.approvals',
    ];

    /**
     * Help articles data structure
     */
    protected array $articles = [
        [
            'id' => 'dashboard-overview',
            'category' => 'getting_started',
            'title' => 'portal.help.articles.dashboard_overview.title',
            'description' => 'portal.help.articles.dashboard_overview.description',
            'content' => 'portal.help.articles.dashboard_overview.content',
            'icon' => 'dashboard',
        ],
        [
            'id' => 'submit-ticket',
            'category' => 'helpdesk',
            'title' => 'portal.help.articles.submit_ticket.title',
            'description' => 'portal.help.articles.submit_ticket.description',
            'content' => 'portal.help.articles.submit_ticket.content',
            'icon' => 'ticket',
        ],
        [
            'id' => 'track-ticket',
            'category' => 'helpdesk',
            'title' => 'portal.help.articles.track_ticket.title',
            'description' => 'portal.help.articles.track_ticket.description',
            'content' => 'portal.help.articles.track_ticket.content',
            'icon' => 'search',
        ],
        [
            'id' => 'request-loan',
            'category' => 'loans',
            'title' => 'portal.help.articles.request_loan.title',
            'description' => 'portal.help.articles.request_loan.description',
            'content' => 'portal.help.articles.request_loan.content',
            'icon' => 'loan',
        ],
        [
            'id' => 'manage-profile',
            'category' => 'profile',
            'title' => 'portal.help.articles.manage_profile.title',
            'description' => 'portal.help.articles.manage_profile.description',
            'content' => 'portal.help.articles.manage_profile.content',
            'icon' => 'user',
        ],
        [
            'id' => 'notification-preferences',
            'category' => 'profile',
            'title' => 'portal.help.articles.notification_preferences.title',
            'description' => 'portal.help.articles.notification_preferences.description',
            'content' => 'portal.help.articles.notification_preferences.content',
            'icon' => 'bell',
        ],
        [
            'id' => 'approval-process',
            'category' => 'approvals',
            'title' => 'portal.help.articles.approval_process.title',
            'description' => 'portal.help.articles.approval_process.description',
            'content' => 'portal.help.articles.approval_process.content',
            'icon' => 'check-circle',
        ],
    ];

    /**
     * Get categories
     */
    #[Computed]
    public function categories(): array
    {
        return $this->categories;
    }

    /**
     * Get filtered articles
     */
    #[Computed]
    public function filteredArticles(): \Illuminate\Support\Collection
    {
        $articles = collect($this->articles);

        // Filter by category
        if ($this->selectedCategory !== 'all') {
            $articles = $articles->where('category', $this->selectedCategory);
        }

        // Filter by search term
        if (! empty($this->search)) {
            $searchLower = mb_strtolower($this->search);
            $articles = $articles->filter(function ($article) use ($searchLower) {
                $title = mb_strtolower(__($article['title']));
                $description = mb_strtolower(__($article['description']));

                return str_contains($title, $searchLower) ||
                       str_contains($description, $searchLower);
            });
        }

        return $articles;
    }

    /**
     * Select category
     */
    public function selectCategory(string $category): void
    {
        $this->selectedCategory = $category;
        $this->resetPage();
    }

    /**
     * Clear search
     */
    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Reset pagination when filters change
     */
    public function updated($property): void
    {
        if (in_array($property, ['search', 'selectedCategory'])) {
            $this->resetPage();
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.portal.help.help-center', [
            'categories' => $this->categories,
            'articles' => $this->filteredArticles,
        ]);
    }
}
