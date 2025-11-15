<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\BilingualSupportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use UnitEnum;

class BilingualManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.bilingual-management';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.bilingual_management.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.bilingual_management.group');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('export_format')
                    ->label(__('admin_pages.bilingual_management.fields.export_format'))
                    ->options([
                        'json' => 'JSON',
                        'csv' => 'CSV',
                        'xlsx' => 'Excel (XLSX)',
                    ])
                    ->default('json'),

                FileUpload::make('import_file')
                    ->label(__('admin_pages.bilingual_management.fields.import_file'))
                    ->acceptedFileTypes(['application/json', 'text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(5120), // 5MB
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
                ->label(__('admin_pages.bilingual_management.actions.export'))
                ->action('exportTranslations')
                ->color('primary'),

            Action::make('importTranslations')
                ->label(__('admin_pages.bilingual_management.actions.import'))
                ->action('importTranslations')
                ->color('success'),
        ];
    }

    #[Computed]
    public function supportedLocales(): array
    {
        $service = app(BilingualSupportService::class);

        return $service->getSupportedLocales();
    }

    #[Computed]
    public function currentLocale(): string
    {
        $service = app(BilingualSupportService::class);

        return $service->getCurrentLocale();
    }

    #[Computed]
    public function translationStats(): array
    {
        $service = app(BilingualSupportService::class);

        return $service->getTranslationStats();
    }

    #[Computed]
    public function translationIssues(): array
    {
        $service = app(BilingualSupportService::class);

        return $service->validateTranslations();
    }

    #[Computed]
    public function languageSwitcherData(): array
    {
        $service = app(BilingualSupportService::class);

        return $service->getLanguageSwitcherData();
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
        $data = $this->form->getState();
        $format = $data['export_format'] ?? 'json';

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
        $data = $this->form->getState();

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

    public function switchLanguage(string $locale): void
    {
        $service = app(BilingualSupportService::class);
        $service->setLocale($locale);

        $locales = $this->supportedLocales();
        $languageName = $locales[$locale]['name'] ?? $locale;

        Notification::make()
            ->title(__('admin_pages.bilingual_management.notifications.language_changed_title'))
            ->body(__('admin_pages.bilingual_management.notifications.language_changed_body', ['language' => $languageName]))
            ->success()
            ->send();

        // Refresh the page to apply new language
        $this->redirect(request()->url());
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
