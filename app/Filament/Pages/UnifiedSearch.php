<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\GlobalSearchService;
use App\Services\SearchHistoryService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * Unified Search Page
 *
 * Global search functionality across tickets, loans, assets, and users.
 * Provides combined results view with relevance ranking and quick preview.
 *
 * @trace Requirements 7.4, 11.1
 */
class UnifiedSearch extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected string $view = 'filament.pages.unified-search';

    protected static ?string $title = 'Carian Menyeluruh';

    protected static ?string $navigationLabel = 'Carian Menyeluruh';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 1;

    public ?string $search = '';

    public array $filters = [];

    public array $results = [];

    public array $suggestions = [];

    public array $recentSearches = [];

    protected GlobalSearchService $searchService;

    protected SearchHistoryService $historyService;

    public function boot(): void
    {
        $this->searchService = app(GlobalSearchService::class);
        $this->historyService = app(SearchHistoryService::class);

        // Load recent searches
        $this->recentSearches = $this->historyService->getRecentSearches(auth()->user());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('search')
                    ->label('Cari')
                    ->placeholder('Cari tiket, pinjaman, aset, atau pengguna...')
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn () => $this->performSearch())
                    ->suffixIcon(Heroicon::OutlineMagnifyingGlass)
                    ->helperText('Gunakan Ctrl+K untuk fokus cepat'),

                Select::make('filters.resource')
                    ->label('Sumber')
                    ->options([
                        'all' => 'Semua',
                        'tickets' => 'Tiket',
                        'loans' => 'Pinjaman',
                        'assets' => 'Aset',
                        'users' => 'Pengguna',
                    ])
                    ->default('all')
                    ->live()
                    ->afterStateUpdated(fn () => $this->performSearch()),

                Select::make('filters.date_range')
                    ->label('Julat Tarikh')
                    ->options([
                        'all' => 'Semua',
                        'today' => 'Hari ini',
                        'week' => '7 hari lepas',
                        'month' => '30 hari lepas',
                        'year' => 'Tahun ini',
                    ])
                    ->default('all')
                    ->live()
                    ->afterStateUpdated(fn () => $this->performSearch()),
            ])
            ->columns(3);
    }

    public function performSearch(): void
    {
        if (empty($this->search) || strlen($this->search) < 2) {
            $this->results = [];
            $this->suggestions = [];

            return;
        }

        // Apply date range filter
        $searchFilters = $this->filters;
        if (! empty($this->filters['date_range']) && $this->filters['date_range'] !== 'all') {
            $searchFilters = array_merge($searchFilters, $this->getDateRangeFilter($this->filters['date_range']));
        }

        // Perform search using the service
        $searchResults = $this->searchService->search($this->search, $searchFilters);

        // Filter by resource if specified
        if (! empty($this->filters['resource']) && $this->filters['resource'] !== 'all') {
            $resourceKey = $this->filters['resource'];
            $this->results = [$resourceKey => $searchResults[$resourceKey] ?? collect()];
        } else {
            $this->results = $searchResults;
        }

        $this->suggestions = $searchResults['suggestions'] ?? [];

        // Record search in history
        $totalResults = collect($this->results)->sum(fn ($results) => is_countable($results) ? count($results) : 0);
        $this->historyService->recordSearch(
            auth()->user(),
            $this->search,
            $this->filters['resource'] ?? 'global',
            $this->filters,
            $totalResults
        );
    }

    protected function getDateRangeFilter(string $range): array
    {
        return match ($range) {
            'today' => [
                'date_from' => now()->startOfDay()->toDateString(),
                'date_to' => now()->endOfDay()->toDateString(),
            ],
            'week' => [
                'date_from' => now()->subDays(7)->toDateString(),
                'date_to' => now()->toDateString(),
            ],
            'month' => [
                'date_from' => now()->subDays(30)->toDateString(),
                'date_to' => now()->toDateString(),
            ],
            'year' => [
                'date_from' => now()->startOfYear()->toDateString(),
                'date_to' => now()->endOfYear()->toDateString(),
            ],
            default => [],
        };
    }

    public function useRecentSearch(string $query): void
    {
        $this->search = $query;
        $this->performSearch();
    }

    public function useSuggestion(string $query): void
    {
        $this->search = $query;
        $this->performSearch();
    }

    public function getAllResults(): Collection
    {
        $allResults = collect();

        foreach ($this->results as $type => $results) {
            foreach ($results as $result) {
                $result['type'] = $type;
                $allResults->push($result);
            }
        }

        return $allResults->sortByDesc('relevance');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }
}
