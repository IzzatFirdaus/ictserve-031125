<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Help Center Component
 *
 * Provides searchable knowledge base with categories, articles,
 * and support contact functionality.
 *
 * @version 1.0.0
 *
 * @since 2025-11-06
 *
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.3: Searchable knowledge base with categories
 * - WCAG 2.2 AA: Keyboard navigation, ARIA labels, semantic HTML
 * - D12 §4: Unified component library integration
 */
class HelpCenter extends Component
{
    use WithPagination;

    /**
     * Search query
     */
    public string $search = '';

    /**
     * Selected category filter
     */
    public ?string $selectedCategory = null;

    /**
     * Help article categories
     */
    public array $categories = [];

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->categories = $this->getCategories();
    }

    /**
     * Get help categories
     */
    protected function getCategories(): array
    {
        return [
            'getting_started' => [
                'name' => __('portal.help.getting_started'),
                'icon' => 'rocket-launch',
                'articles_count' => 5,
            ],
            'helpdesk_tickets' => [
                'name' => __('portal.help.helpdesk_tickets'),
                'icon' => 'ticket',
                'articles_count' => 8,
            ],
            'asset_loans' => [
                'name' => __('portal.help.asset_loans'),
                'icon' => 'cube',
                'articles_count' => 6,
            ],
            'profile_management' => [
                'name' => __('portal.help.profile_management'),
                'icon' => 'user-circle',
                'articles_count' => 4,
            ],
            'approvals' => [
                'name' => __('portal.help.approvals'),
                'icon' => 'check-circle',
                'articles_count' => 3,
            ],
        ];
    }

    /**
     * Get help articles
     */
    public function getArticlesProperty(): Collection
    {
        $articles = $this->getAllArticles();

        // Filter by category
        if ($this->selectedCategory) {
            $articles = $articles->where('category', $this->selectedCategory);
        }

        // Filter by search query
        if ($this->search) {
            $articles = $articles->filter(function ($article) {
                return str_contains(strtolower($article['title']), strtolower($this->search))
                    || str_contains(strtolower($article['content']), strtolower($this->search));
            });
        }

        return $articles;
    }

    /**
     * Get all help articles
     */
    protected function getAllArticles(): Collection
    {
        // In production, this would fetch from database
        // For now, return sample articles
        return collect([
            [
                'id' => 1,
                'category' => 'getting_started',
                'title' => 'How to navigate the portal',
                'content' => 'Learn how to use the main navigation menu and access different sections of the portal.',
                'views' => 245,
                'helpful_votes' => 42,
                'created_at' => now()->subDays(30),
            ],
            [
                'id' => 2,
                'category' => 'getting_started',
                'title' => 'Understanding your dashboard',
                'content' => 'Your dashboard provides an overview of your submissions, recent activity, and quick actions.',
                'views' => 189,
                'helpful_votes' => 38,
                'created_at' => now()->subDays(25),
            ],
            [
                'id' => 3,
                'category' => 'helpdesk_tickets',
                'title' => 'How to submit a helpdesk ticket',
                'content' => 'Step-by-step guide to creating and submitting helpdesk tickets for ICT support.',
                'views' => 312,
                'helpful_votes' => 56,
                'created_at' => now()->subDays(20),
            ],
            [
                'id' => 4,
                'category' => 'helpdesk_tickets',
                'title' => 'Tracking your ticket status',
                'content' => 'Learn how to monitor the progress of your helpdesk tickets and receive notifications.',
                'views' => 278,
                'helpful_votes' => 51,
                'created_at' => now()->subDays(18),
            ],
            [
                'id' => 5,
                'category' => 'asset_loans',
                'title' => 'Requesting asset loans',
                'content' => 'Complete guide to requesting ICT equipment loans with approval workflow.',
                'views' => 201,
                'helpful_votes' => 45,
                'created_at' => now()->subDays(15),
            ],
            [
                'id' => 6,
                'category' => 'asset_loans',
                'title' => 'Returning borrowed assets',
                'content' => 'Important information about asset return procedures and deadlines.',
                'views' => 167,
                'helpful_votes' => 39,
                'created_at' => now()->subDays(12),
            ],
            [
                'id' => 7,
                'category' => 'profile_management',
                'title' => 'Updating your profile',
                'content' => 'How to update your contact information and notification preferences.',
                'views' => 145,
                'helpful_votes' => 32,
                'created_at' => now()->subDays(10),
            ],
            [
                'id' => 8,
                'category' => 'profile_management',
                'title' => 'Managing notification preferences',
                'content' => 'Customize which email notifications you receive from the portal.',
                'views' => 123,
                'helpful_votes' => 28,
                'created_at' => now()->subDays(8),
            ],
            [
                'id' => 9,
                'category' => 'approvals',
                'title' => 'Approving loan applications',
                'content' => 'Guide for Grade 41+ officers on reviewing and approving loan requests.',
                'views' => 98,
                'helpful_votes' => 22,
                'created_at' => now()->subDays(5),
            ],
        ]);
    }

    /**
     * Get popular articles
     */
    public function getPopularArticlesProperty(): Collection
    {
        return $this->getAllArticles()
            ->sortByDesc('views')
            ->take(5);
    }

    /**
     * Get recent articles
     */
    public function getRecentArticlesProperty(): Collection
    {
        return $this->getAllArticles()
            ->sortByDesc('created_at')
            ->take(5);
    }

    /**
     * Select category
     */
    public function selectCategory(?string $category): void
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
        $this->selectedCategory = null;
        $this->resetPage();
    }

    /**
     * Updated search property
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.portal.help-center', [
            'articles' => $this->articles,
            'popularArticles' => $this->popularArticles,
            'recentArticles' => $this->recentArticles,
        ]);
    }
}
