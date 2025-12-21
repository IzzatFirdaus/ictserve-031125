<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\BilingualSupportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Livewire\Attributes\Computed;
use UnitEnum;

class BilingualManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.bilingual-management';

    /** @var array<string, mixed> */
    public array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        abort(404);
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.bilingual_management.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system_management');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Statistik Terjemahan')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('ms_stats')
                            ->label('Bahasa Melayu')
                            ->content(function () {
                                $stats = $this->translationStats()['ms'] ?? [];

                                return sprintf(
                                    'Jumlah: %d | Diterjemah: %d | Hilang: %d | Lengkap: %.1f%%',
                                    $stats['total_keys'] ?? 0,
                                    $stats['translated_keys'] ?? 0,
                                    ($stats['total_keys'] ?? 0) - ($stats['translated_keys'] ?? 0),
                                    $stats['completion_percentage'] ?? 0
                                );
                            }),
                    ])
                    ->columns(1),

                \Filament\Schemas\Components\Section::make('Import/Eksport')
                    ->schema([
                        Select::make('export_format')
                            ->label(__('admin_pages.bilingual_management.fields.export_format'))
                            ->options([
                                'json' => 'JSON',
                                'csv' => 'CSV',
                                'xlsx' => 'Excel (XLSX)',
                            ])
                            ->default('json'),
                    ]),

                \Filament\Schemas\Components\Section::make('Panduan')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('guidelines')
                            ->label('')
                            ->content(fn () => view('filament.components.translation-guidelines')),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('validateTranslations')
                ->label(__('admin_pages.bilingual_management.actions.validate'))
                ->action('validateTranslations')
                ->color('warning'),

            Action::make('exportTranslations')
                ->label('Eksport Terjemahan')
                ->action('exportTranslations')
                ->color('primary'),

            Action::make('importTranslations')
                ->label('Import Terjemahan')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('import_file')
                        ->label('Fail Import')
                        ->acceptedFileTypes(['application/json', 'text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->maxSize(5120)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->data['import_file'] = $data['import_file'];
                    $this->importTranslations();
                })
                ->color('success'),
        ];
    }

    /**
     * @return array<string, array{total_keys: int, translated_keys: int, completion_percentage: float}>
     */
    #[Computed]
    public function translationStats(): array
    {
        $service = app(BilingualSupportService::class);

        return $service->getTranslationStats();
    }

    /**
     * @return array<string, array<int, string>>
     */
    #[Computed]
    public function translationIssues(): array
    {
        $service = app(BilingualSupportService::class);

        return $service->validateTranslations();
    }

    public function validateTranslations(): void
    {
        $issues = $this->translationIssues();

        if (empty($issues)) {
            Notification::make()
                ->title(__('admin_pages.bilingual_management.notifications.validation_complete_title'))
                ->body(__('admin_pages.bilingual_management.notifications.validation_complete_body'))
                ->success()
                ->send();
        } else {
            $missingCount = count($issues['missing'] ?? []);
            $emptyCount = count($issues['empty'] ?? []);

            Notification::make()
                ->title(__('admin_pages.bilingual_management.notifications.validation_issues_title'))
                ->body(__('admin_pages.bilingual_management.notifications.validation_issues_body', ['missing' => $missingCount, 'empty' => $emptyCount]))
                ->warning()
                ->send();
        }
    }

    public function exportTranslations(): void
    {
        $data = $this->data;
        $format = is_string($data['export_format'] ?? null) ? $data['export_format'] : 'json';

        $service = app(BilingualSupportService::class);
        $content = $service->exportTranslations($format);

        $filename = 'translations_'.now()->format('Y-m-d_H-i-s').'.'.$format;

        // In a real implementation, this would trigger a download
        Notification::make()
            ->title(__('admin_pages.bilingual_management.notifications.export_complete_title'))
            ->body(__('admin_pages.bilingual_management.notifications.export_complete_body', ['filename' => $filename]))
            ->success()
            ->send();
    }

    public function importTranslations(): void
    {
        $data = $this->data;

        if (empty($data['import_file'])) {
            Notification::make()
                ->title(__('admin_pages.bilingual_management.notifications.no_file_title'))
                ->body(__('admin_pages.bilingual_management.notifications.no_file_body'))
                ->warning()
                ->send();

            return;
        }

        $service = app(BilingualSupportService::class);

        // In a real implementation, this would read the uploaded file
        $success = $service->importTranslations('{}', 'json');

        if ($success) {
            Notification::make()
                ->title(__('admin_pages.bilingual_management.notifications.import_complete_title'))
                ->body(__('admin_pages.bilingual_management.notifications.import_complete_body'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('admin_pages.bilingual_management.notifications.import_failed_title'))
                ->body(__('admin_pages.bilingual_management.notifications.import_failed_body'))
                ->danger()
                ->send();
        }
    }

    public function getCompletionColor(float $percentage): string
    {
        return match (true) {
            $percentage >= 95 => 'success',
            $percentage >= 80 => 'warning',
            default => 'danger',
        };
    }
}
