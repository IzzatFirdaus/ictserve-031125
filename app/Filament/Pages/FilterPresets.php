<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Models\User;
use App\Services\FilterPresetService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Filter Presets Management Page
 *
 * Allows users to create, manage, and apply saved filter presets
 * across different resources for quick access to common filter combinations.
 *
 * @trace Requirements 11.2, 11.3
 */
class FilterPresets extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-funnel';

    protected string $view = 'filament.pages.filter-presets';

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    /** @var array<string, array<string, mixed>> */
    public array $presets = [];

    public string $selectedResource = 'helpdesk-tickets';

    protected FilterPresetService $presetService;

    public function boot(): void
    {
        $this->presetService = app(FilterPresetService::class);
        $this->loadPresets();
    }

    public function loadPresets(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->presets = $this->presetService->getUserPresets($user, $this->selectedResource);
    }

    public function updatedSelectedResource(): void
    {
        $this->loadPresets();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.filter_presets.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system');
    }

    public function getTitle(): string
    {
        return __('admin_pages.filter_presets.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Cipta Preset Baharu')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    TextInput::make('name')
                        ->label('Nama Preset')
                        ->required()
                        ->maxLength(100),

                    Select::make('resource')
                        ->label('Sumber')
                        ->options([
                            'helpdesk-tickets' => 'Tiket Helpdesk',
                            'loan-applications' => 'Permohonan Pinjaman',
                            'assets' => 'Aset',
                            'users' => 'Pengguna',
                        ])
                        ->default($this->selectedResource)
                        ->required(),

                    Checkbox::make('is_default')
                        ->label('Jadikan sebagai preset lalai'),
                ])
                ->action(function (array $data): void {
                    // For demo purposes, create a sample filter
                    $sampleFilters = $this->getSampleFilters($data['resource']);

                    /** @var User $user */
                    $user = Auth::user();
                    $this->presetService->saveFilterPreset(
                        $user,
                        $data['resource'],
                        $data['name'],
                        $sampleFilters,
                        $data['is_default'] ?? false
                    );

                    $this->loadPresets();

                    Notification::make()
                        ->title('Preset berjaya dicipta')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function deletePreset(string $presetId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->presetService->deletePreset($user, $this->selectedResource, $presetId);
        $this->loadPresets();

        Notification::make()
            ->title('Preset berjaya dipadam')
            ->success()
            ->send();
    }

    public function setAsDefault(string $presetId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->presetService->updatePreset(
            $user,
            $this->selectedResource,
            $presetId,
            ['is_default' => true]
        );

        $this->loadPresets();

        Notification::make()
            ->title('Preset ditetapkan sebagai lalai')
            ->success()
            ->send();
    }

    public function applyPreset(string $presetId): void
    {
        $preset = $this->presets[$presetId] ?? null;

        if (! $preset) {
            Notification::make()
                ->title('Preset tidak dijumpai')
                ->danger()
                ->send();

            return;
        }

        // Generate URL with filters
        $baseUrl = $this->getResourceUrl($this->selectedResource);
        $filters = is_array($preset['filters'] ?? null) ? $preset['filters'] : [];
        $filterUrl = $this->presetService->generateFilterUrl($baseUrl, $filters);

        // Redirect to the filtered resource
        $this->redirect($filterUrl);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getSampleFilters(string $resource): array
    {
        return match ($resource) {
            'helpdesk-tickets' => [
                'status' => ['open', 'assigned'],
                'priority' => ['high', 'urgent'],
            ],
            'loan-applications' => [
                'status' => ['pending_approval'],
            ],
            'assets' => [
                'status' => ['available'],
            ],
            'users' => [
                'is_active' => '1',
            ],
            default => [],
        };
    }

    protected function getResourceUrl(string $resource): string
    {
        return match ($resource) {
            'helpdesk-tickets' => HelpdeskTicketResource::getUrl('index'),
            'loan-applications' => LoanApplicationResource::getUrl('index'),
            'assets' => route('filament.admin.resources.assets.assets.index'),
            'users' => route('filament.admin.resources.users.users.index'),
            default => '#',
        };
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getQuickFilters(): array
    {
        return $this->presetService->getQuickFilters($this->selectedResource);
    }

    /**
     * @param  array<string, mixed>  $filters
     */

    /**
     * @param  array<string, mixed>  $filters
     */
    public function applyQuickFilter(array $filters): void
    {
        $baseUrl = $this->getResourceUrl($this->selectedResource);
        $filterUrl = $this->presetService->generateFilterUrl($baseUrl, $filters);

        $this->redirect($filterUrl);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }
}
