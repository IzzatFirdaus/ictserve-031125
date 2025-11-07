<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use BackedEnum;
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

    public array $results = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('search')
                    ->label('Cari')
                    ->placeholder('Cari tiket, pinjaman, aset, atau pengguna...')
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn () => $this->performSearch())
                    ->suffixIcon(Heroicon::OutlineMagnifyingGlass),
            ]);
    }

    public function performSearch(): void
    {
        if (empty($this->search) || strlen($this->search) < 2) {
            $this->results = [];

            return;
        }

        $this->results = [
            'tickets' => $this->searchTickets(),
            'loans' => $this->searchLoans(),
            'assets' => $this->searchAssets(),
            'users' => $this->searchUsers(),
        ];
    }

    protected function searchTickets(): Collection
    {
        return HelpdeskTicket::query()
            ->where(function ($query) {
                $query->where('ticket_number', 'like', "%{$this->search}%")
                    ->orWhere('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhere('guest_name', 'like', "%{$this->search}%")
                    ->orWhere('guest_email', 'like', "%{$this->search}%");
            })
            ->with(['user', 'assignedTo', 'asset'])
            ->limit(10)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->ticket_number.' - '.$ticket->title,
                    'subtitle' => 'Status: '.ucfirst(str_replace('_', ' ', $ticket->status)),
                    'description' => $ticket->description,
                    'url' => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $ticket),
                    'icon' => 'heroicon-o-ticket',
                    'color' => match ($ticket->status) {
                        'open' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'closed' => 'secondary',
                        default => 'gray',
                    },
                    'relevance' => $this->calculateRelevance($ticket->ticket_number.' '.$ticket->title),
                ];
            });
    }

    protected function searchLoans(): Collection
    {
        return LoanApplication::query()
            ->where(function ($query) {
                $query->where('application_number', 'like', "%{$this->search}%")
                    ->orWhere('applicant_name', 'like', "%{$this->search}%")
                    ->orWhere('applicant_email', 'like', "%{$this->search}%")
                    ->orWhere('purpose', 'like', "%{$this->search}%");
            })
            ->with(['user', 'loanItems.asset'])
            ->limit(10)
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'title' => $loan->application_number.' - '.$loan->applicant_name,
                    'subtitle' => 'Status: '.ucfirst(str_replace('_', ' ', $loan->status)),
                    'description' => $loan->purpose,
                    'url' => route('filament.admin.resources.loans.loan-applications.view', $loan),
                    'icon' => 'heroicon-o-cube',
                    'color' => match ($loan->status) {
                        'pending_approval' => 'warning',
                        'approved' => 'success',
                        'in_use' => 'info',
                        'completed' => 'secondary',
                        'rejected' => 'danger',
                        default => 'gray',
                    },
                    'relevance' => $this->calculateRelevance($loan->application_number.' '.$loan->applicant_name),
                ];
            });
    }

    protected function searchAssets(): Collection
    {
        return Asset::query()
            ->where(function ($query) {
                $query->where('asset_code', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('brand', 'like', "%{$this->search}%")
                    ->orWhere('model', 'like', "%{$this->search}%")
                    ->orWhere('serial_number', 'like', "%{$this->search}%");
            })
            ->with(['category'])
            ->limit(10)
            ->get()
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'title' => $asset->asset_code.' - '.$asset->name,
                    'subtitle' => 'Status: '.ucfirst(str_replace('_', ' ', $asset->status)),
                    'description' => $asset->category?->name_en.' | '.$asset->brand.' '.$asset->model,
                    'url' => route('filament.admin.resources.assets.assets.view', $asset),
                    'icon' => 'heroicon-o-server',
                    'color' => match ($asset->status) {
                        'available' => 'success',
                        'on_loan' => 'warning',
                        'maintenance' => 'danger',
                        'retired' => 'secondary',
                        default => 'gray',
                    },
                    'relevance' => $this->calculateRelevance($asset->asset_code.' '.$asset->name),
                ];
            });
    }

    protected function searchUsers(): Collection
    {
        if (! auth()->user()->hasRole('superuser')) {
            return collect();
        }

        return User::query()
            ->where(function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('staff_id', 'like', "%{$this->search}%");
            })
            ->with(['division', 'grade'])
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'description' => $user->division?->name_ms.' | '.$user->grade?->name,
                    'url' => route('filament.admin.resources.users.users.view', $user),
                    'icon' => 'heroicon-o-user',
                    'color' => $user->is_active ? 'success' : 'secondary',
                    'relevance' => $this->calculateRelevance($user->name.' '.$user->email),
                ];
            });
    }

    protected function calculateRelevance(string $text): int
    {
        $searchLower = strtolower($this->search);
        $textLower = strtolower($text);

        // Exact match gets highest score
        if ($textLower === $searchLower) {
            return 100;
        }

        // Starts with search term gets high score
        if (str_starts_with($textLower, $searchLower)) {
            return 90;
        }

        // Contains search term gets medium score
        if (str_contains($textLower, $searchLower)) {
            return 70;
        }

        // Word match gets lower score
        $words = explode(' ', $searchLower);
        $score = 0;
        foreach ($words as $word) {
            if (str_contains($textLower, $word)) {
                $score += 10;
            }
        }

        return min($score, 60);
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
