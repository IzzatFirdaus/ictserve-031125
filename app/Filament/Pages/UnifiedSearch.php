<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\UnifiedSearchService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Unified Search Page
 *
 * Global search across tickets, loans, assets, and users with keyboard shortcuts.
 *
 * @see D03-FR-009.1 Global search requirements
 * @see D03-FR-012.1 Advanced search and filtering
 * @see D04 §9.1 Search architecture
 */
class UnifiedSearch extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected string $view = 'filament.pages.unified-search';

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    public string $search = '';

    public array $results = [];

    public array $selectedResources = ['tickets', 'loans', 'assets', 'users'];

    public int $limit = 10;

    public bool $isLoading = false;

    /**
     * Control navigation visibility based on user permissions
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()?->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Get navigation badge
     */
    public static function getNavigationBadge(): ?string
    {
        return 'Ctrl+K';
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.unified_search.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.unified_search.group');
    }

    public function getTitle(): string
    {
        return __('admin_pages.unified_search.title');
    }

    /**
     * Get navigation badge color
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    /**
     * Perform search
     */
    public function performSearch(): void
    {
        if (strlen($this->search) < 2) {
            $this->results = [];

            return;
        }

        $this->isLoading = true;

        $searchService = app(UnifiedSearchService::class);
        $this->results = $searchService->search(
            $this->search,
            $this->selectedResources,
            $this->limit
        );

        $this->isLoading = false;
    }

    /**
     * Clear search
     */
    public function clearSearch(): void
    {
        $this->search = '';
        $this->results = [];
    }

    /**
     * Toggle resource filter
     */
    public function toggleResource(string $resource): void
    {
        if (in_array($resource, $this->selectedResources)) {
            $this->selectedResources = array_values(
                array_diff($this->selectedResources, [$resource])
            );
        } else {
            $this->selectedResources[] = $resource;
        }

        if (! empty($this->search)) {
            $this->performSearch();
        }
    }

    /**
     * Get total results count
     */
    public function getTotalResultsProperty(): int
    {
        return collect($this->results)->sum(fn ($items) => count($items));
    }

    /**
     * Mount the page
     */
    public function mount(): void
    {
        // Initialize with empty results
        $this->results = [];
    }
}
