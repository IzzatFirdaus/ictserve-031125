<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\FilterPresetService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
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

    protected static ?string $title = 'Preset Penapis';

    protected static ?string $navigationLabel = 'Preset Penapis';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 2;

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
        $this->presets = $this->presetService->getUserPresets(auth()->user(), $this->selectedResource);
    }

    public function updatedSelectedResource(): void
    {
        $this->loadPresets();
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

                    $this->presetService->saveFilterPreset(
                        auth()->user(),
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
        $this->presetService->deletePreset(auth()->user(), $this->selectedResource, $presetId);
        $this->loadPresets();

        Notification::make()
            ->title('Preset berjaya dipadam')
            ->success()
            ->send();
    }

    public function setAsDefault(string $presetId): void
    {
        $this->presetService->updatePreset(
            auth()->user(),
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
        $filterUrl = $this->presetService->generateFilterUrl($baseUrl, $preset['filters']);

        // Redirect to the filtered resource
        $this->redirect($filterUrl);
    }

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
            'helpdesk-tickets' => route('filament.admin.resources.helpdesk.helpdesk-tickets.index'),
            'loan-applications' => route('filament.admin.resources.loans.loan-applications.index'),
            'assets' => route('filament.admin.resources.assets.assets.index'),
            'users' => route('filament.admin.resources.users.users.index'),
            default => '#',
        };
    }

    public function getQuickFilters(): array
    {
        return $this->presetService->getQuickFilters($this->selectedResource);
    }

    public function applyQuickFilter(array $filters): void
    {
        $baseUrl = $this->getResourceUrl($this->selectedResource);
        $filterUrl = $this->presetService->generateFilterUrl($baseUrl, $filters);

        $this->redirect($filterUrl);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }
}
