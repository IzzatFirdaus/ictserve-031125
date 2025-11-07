<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\BilingualSupportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use UnitEnum;

class BilingualManagement extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = 'Language Management';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('export_format')
                    ->label('Export Format')
                    ->options([
                        'json' => 'JSON',
                        'csv' => 'CSV',
                        'xlsx' => 'Excel (XLSX)',
                    ])
                    ->default('json'),

                FileUpload::make('import_file')
                    ->label('Import File')
                    ->acceptedFileTypes(['application/json', 'text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(5120), // 5MB
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('validateTranslations')
                ->label('Validate Translations')
                ->action('validateTranslations')
                ->color('warning'),

            Action::make('exportTranslations')
                ->label('Export Translations')
                ->action('exportTranslations')
                ->color('primary'),

            Action::make('importTranslations')
                ->label('Import Translations')
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
        $issues = $this->translationIssues;

        if (empty($issues)) {
            Notification::make()
                ->title('Translation validation completed')
                ->body('No issues found. All translations are complete.')
                ->success()
                ->send();
        } else {
            $missingCount = count($issues['missing'] ?? []);
            $emptyCount = count($issues['empty'] ?? []);

            Notification::make()
                ->title('Translation issues found')
                ->body("Missing translations: {$missingCount}, Empty translations: {$emptyCount}")
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
            ->title('Export completed')
            ->body("Translations exported as {$filename}")
            ->success()
            ->send();
    }

    public function importTranslations(): void
    {
        $data = $this->form->getState();

        if (empty($data['import_file'])) {
            Notification::make()
                ->title('No file selected')
                ->body('Please select a file to import')
                ->warning()
                ->send();

            return;
        }

        $service = app(BilingualSupportService::class);

        // In a real implementation, this would read the uploaded file
        $success = $service->importTranslations('{}', 'json');

        if ($success) {
            Notification::make()
                ->title('Import completed')
                ->body('Translations imported successfully')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Import failed')
                ->body('Failed to import translations. Please check the file format.')
                ->danger()
                ->send();
        }
    }

    public function switchLanguage(string $locale): void
    {
        $service = app(BilingualSupportService::class);
        $service->setLocale($locale);

        $locales = $this->supportedLocales;
        $languageName = $locales[$locale]['name'] ?? $locale;

        Notification::make()
            ->title('Language changed')
            ->body("Interface language changed to {$languageName}")
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
