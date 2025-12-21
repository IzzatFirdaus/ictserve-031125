<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\SLAThresholdService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

use function trans;

/**
 * SLA Threshold Management Page
 *
 * Superuser-only page for managing SLA thresholds, response times,
 * resolution times, escalation rules, and notification settings.
 *
 * @trace Requirements 12.2, 5.2, 5.5
 */
class SLAThresholdManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    protected string $view = 'filament.pages.sla-threshold-management';

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed> */
    public array $thresholds = [];

    protected SLAThresholdService $slaService;

    public function boot(): void
    {
        $this->slaService = app(SLAThresholdService::class);
        $this->loadThresholds();
    }

    public function mount(): void
    {
        $this->loadThresholds();
    }

    public function loadThresholds(): void
    {
        $this->thresholds = $this->slaService->getSLAThresholds();
    }

    protected function getForms(): array
    {
        return [
            'form' => Schema::make($this)
                ->schema([
                    Section::make('Kritikal')
                        ->description('Masa respons dan penyelesaian untuk tiket kritikal')
                        ->icon('heroicon-o-fire')
                        ->iconColor('danger')
                        ->schema([
                            TextInput::make('thresholds.critical.response_time')
                                ->label('Masa Respons')
                                ->numeric()
                                ->minValue(1)
                                ->default(15)
                                ->suffix('Minit')
                                ->required(),
                            TextInput::make('thresholds.critical.resolution_time')
                                ->label('Masa Penyelesaian')
                                ->numeric()
                                ->minValue(1)
                                ->default(4)
                                ->suffix('Jam')
                                ->required(),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    Section::make('Tinggi')
                        ->description('Masa respons dan penyelesaian untuk tiket keutamaan tinggi')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->iconColor('warning')
                        ->schema([
                            TextInput::make('thresholds.high.response_time')
                                ->label('Masa Respons')
                                ->numeric()
                                ->minValue(1)
                                ->default(30)
                                ->suffix('Minit')
                                ->required(),
                            TextInput::make('thresholds.high.resolution_time')
                                ->label('Masa Penyelesaian')
                                ->numeric()
                                ->minValue(1)
                                ->default(8)
                                ->suffix('Jam')
                                ->required(),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    Section::make('Normal')
                        ->description('Masa respons dan penyelesaian untuk tiket biasa')
                        ->icon('heroicon-o-clock')
                        ->iconColor('primary')
                        ->schema([
                            TextInput::make('thresholds.normal.response_time')
                                ->label('Masa Respons')
                                ->numeric()
                                ->minValue(1)
                                ->default(60)
                                ->suffix('Minit')
                                ->required(),
                            TextInput::make('thresholds.normal.resolution_time')
                                ->label('Masa Penyelesaian')
                                ->numeric()
                                ->minValue(1)
                                ->default(24)
                                ->suffix('Jam')
                                ->required(),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    Section::make('Rendah')
                        ->description('Masa respons dan penyelesaian untuk tiket keutamaan rendah')
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->schema([
                            TextInput::make('thresholds.low.response_time')
                                ->label('Masa Respons')
                                ->numeric()
                                ->minValue(1)
                                ->default(120)
                                ->suffix('Minit')
                                ->required(),
                            TextInput::make('thresholds.low.resolution_time')
                                ->label('Masa Penyelesaian')
                                ->numeric()
                                ->minValue(1)
                                ->default(48)
                                ->suffix('Jam')
                                ->required(),
                        ])
                        ->columns(2)
                        ->collapsible(),
                    Fieldset::make(__('sla.form.escalation.fieldset'))
                        ->schema([
                            Checkbox::make('thresholds.escalation.enabled')
                                ->label(__('sla.form.escalation.enabled'))
                                ->default(true),

                            TextInput::make('thresholds.escalation.threshold_percent')
                                ->label(__('sla.form.escalation.threshold_percent'))
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(50)
                                ->default(25)
                                ->suffix('%')
                                ->helperText(__('sla.form.escalation.helper')),

                            Select::make('thresholds.escalation.escalation_roles')
                                ->label(__('sla.form.escalation.roles.label'))
                                ->multiple()
                                ->options(trans('sla.form.escalation.roles.options'))
                                ->default(['admin', 'superuser']),

                            Checkbox::make('thresholds.escalation.auto_assign')
                                ->label(__('sla.form.escalation.auto_assign'))
                                ->default(true),
                        ])
                        ->columns(2),

                    Fieldset::make(__('sla.form.notifications.fieldset'))
                        ->schema([
                            Checkbox::make('thresholds.notifications.enabled')
                                ->label(__('sla.form.notifications.enabled'))
                                ->default(true),

                            TextInput::make('thresholds.notifications.intervals.warning')
                                ->label(__('sla.form.notifications.warning'))
                                ->numeric()
                                ->minValue(1)
                                ->default(60)
                                ->suffix(__('sla.form.categories.suffix.minutes')),

                            TextInput::make('thresholds.notifications.intervals.critical')
                                ->label(__('sla.form.notifications.critical'))
                                ->numeric()
                                ->minValue(1)
                                ->default(15)
                                ->suffix(__('sla.form.categories.suffix.minutes')),

                            TextInput::make('thresholds.notifications.intervals.overdue')
                                ->label(__('sla.form.notifications.overdue'))
                                ->numeric()
                                ->minValue(30)
                                ->default(240)
                                ->suffix(__('sla.form.categories.suffix.minutes')),

                            Checkbox::make('thresholds.notifications.recipients.assignee')
                                ->label(__('sla.form.notifications.recipients.assignee'))
                                ->default(true),

                            Checkbox::make('thresholds.notifications.recipients.supervisor')
                                ->label(__('sla.form.notifications.recipients.supervisor'))
                                ->default(true),

                            Checkbox::make('thresholds.notifications.recipients.admin')
                                ->label(__('sla.form.notifications.recipients.admin'))
                                ->default(true),
                        ])
                        ->columns(3),

                    Fieldset::make(__('sla.form.business_hours.fieldset'))
                        ->schema([
                            Checkbox::make('thresholds.business_hours.enabled')
                                ->label(__('sla.form.business_hours.enabled'))
                                ->default(true),

                            Select::make('thresholds.business_hours.timezone')
                                ->label(__('sla.form.business_hours.timezone'))
                                ->options(trans('sla.form.business_hours.timezones'))
                                ->default('Asia/Kuala_Lumpur'),

                            TimePicker::make('thresholds.business_hours.start_time')
                                ->label(__('sla.form.business_hours.start'))
                                ->default('08:00'),

                            TimePicker::make('thresholds.business_hours.end_time')
                                ->label(__('sla.form.business_hours.end'))
                                ->default('17:00'),

                            Select::make('thresholds.business_hours.working_days')
                                ->label(__('sla.form.business_hours.working_days'))
                                ->multiple()
                                ->options(function (): array {
                                    $days = trans('sla.form.business_hours.days');

                                    if (! is_array($days)) {
                                        return [];
                                    }

                                    $normalized = [];
                                    foreach ($days as $dayKey => $label) {
                                        $normalizedKey = is_numeric($dayKey) ? (int) $dayKey : $dayKey;
                                        $normalized[$normalizedKey] = $label;
                                    }

                                    return $normalized;
                                })
                                ->default([1, 2, 3, 4, 5]),

                            Checkbox::make('thresholds.business_hours.exclude_weekends')
                                ->label(__('sla.form.business_hours.exclude_weekends'))
                                ->default(true),

                            Checkbox::make('thresholds.business_hours.exclude_holidays')
                                ->label(__('sla.form.business_hours.exclude_holidays'))
                                ->default(true),
                        ])
                        ->columns(3),
                ])
                ->statePath('thresholds'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('sla.actions.save'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->action(function (): void {
                    try {
                        $this->slaService->updateSLAThresholds($this->thresholds);

                        Notification::make()
                            ->title(__('sla.notifications.save_success'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('sla.notifications.save_error'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('test')
                ->label(__('sla.actions.test'))
                ->icon('heroicon-o-beaker')
                ->color('info')
                ->action(function (): void {
                    $this->runSLATests();
                }),

            Action::make('reset')
                ->label(__('sla.actions.reset'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('sla.modals.reset.heading'))
                ->modalDescription(__('sla.modals.reset.description'))
                ->action(function (): void {
                    $this->slaService->clearCache();
                    $this->loadThresholds();

                    Notification::make()
                        ->title(__('sla.notifications.reset_success'))
                        ->success()
                        ->send();
                }),

            Action::make('export')
                ->label(__('sla.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->action(function () {
                    $export = $this->slaService->exportThresholds();
                    $filename = 'sla-thresholds-'.now()->format('Y-m-d-H-i-s').'.json';

                    return response()->streamDownload(
                        fn () => print (json_encode($export, JSON_PRETTY_PRINT)),
                        $filename,
                        ['Content-Type' => 'application/json']
                    );
                }),

            Action::make('import')
                ->label(__('sla.actions.import'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('secondary')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label(__('sla.upload.label'))
                        ->acceptedFileTypes(['application/json'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $content = file_get_contents($data['file']->getRealPath());
                        if ($content === false) {
                            throw new \Exception(__('sla.upload.invalid'));
                        }

                        $importData = json_decode($content, true);

                        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($importData)) {
                            throw new \Exception(__('sla.upload.invalid'));
                        }

                        $this->slaService->importThresholds($importData);
                        $this->loadThresholds();

                        Notification::make()
                            ->title(__('sla.notifications.import_success'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('sla.notifications.import_error'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function runSLATests(): void
    {
        // Test SLA calculations with sample data
        $testCases = [
            ['priority' => 'urgent', 'category' => 'security'],
            ['priority' => 'high', 'category' => 'hardware'],
            ['priority' => 'normal', 'category' => 'software'],
            ['priority' => 'low', 'category' => 'general'],
        ];

        $results = [];
        foreach ($testCases as $test) {
            $sla = $this->slaService->getSLAForTicket($test['priority'], $test['category']);
            $deadlines = $this->slaService->calculateSLADeadlines($test['priority'], $test['category']);

            $results[] = [
                'priority' => $test['priority'],
                'category' => $test['category'],
                'response_hours' => $sla['response_time_hours'],
                'resolution_hours' => $sla['resolution_time_hours'],
                'response_deadline' => $deadlines['response_deadline']->format('d/m/Y H:i'),
                'resolution_deadline' => $deadlines['resolution_deadline']->format('d/m/Y H:i'),
            ];
        }

        Notification::make()
            ->title(__('sla.notifications.test_title'))
            ->body(__('sla.notifications.test_body', ['count' => count($results)]))
            ->info()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return __('sla.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sla.navigation.group');
    }

    public function getTitle(): string
    {
        return __('sla.navigation.title');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }
}
