<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateVersion;
use App\Services\EmailTemplateService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class EmailTemplateManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.email-template-management';

    /** @var array<string, mixed> */
    public array $data = [];

    public ?EmailTemplate $selectedTemplate = null;

    /** @var array<string, mixed> */
    public array $previewData = [];

    /** @var array<int, array<string, mixed>> */
    public array $versionHistory = [];

    public bool $showVersionHistory = false;

    public ?int $compareVersion1 = null;

    public ?int $compareVersion2 = null;

    /** @var array<string, mixed> */
    public array $versionComparison = [];

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('superuser') ?? false;
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.email_templates.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system_management');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getForms(): array
    {
        return [
            'form' => Schema::make($this)
                ->schema([
                    Section::make('Email Template Editor')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Template Name')
                                        ->required()
                                        ->maxLength(255),

                                    Select::make('category')
                                        ->label('Category')
                                        ->options([
                                            'ticket_confirmation' => 'Ticket Confirmation',
                                            'loan_approval' => 'Loan Approval',
                                            'status_update' => 'Status Update',
                                            'reminder' => 'Reminder',
                                            'sla_breach' => 'SLA Breach',
                                        ])
                                        ->required()
                                        ->reactive(),

                                    Select::make('locale')
                                        ->label('Language')
                                        ->options([
                                            'ms' => 'Bahasa Melayu',
                                            'en' => 'English',
                                        ])
                                        ->required()
                                        ->default('ms'),

                                    Toggle::make('is_active')
                                        ->label('Active')
                                        ->default(true),
                                ]),

                            TextInput::make('subject')
                                ->label('Email Subject')
                                ->required()
                                ->maxLength(255)
                                ->helperText('Use {{variable_name}} for dynamic content'),

                            RichEditor::make('body_html')
                                ->label('Email Body (HTML)')
                                ->required()
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'underline',
                                    'link',
                                    'bulletList',
                                    'orderedList',
                                    'h2',
                                    'h3',
                                    'blockquote',
                                ])
                                ->helperText('Use {{variable_name}} for dynamic content. Ensure WCAG 2.2 AA compliance.'),

                            Textarea::make('body_text')
                                ->label('Email Body (Plain Text)')
                                ->rows(8)
                                ->helperText('Plain text version for accessibility'),

                            TextInput::make('change_summary')
                                ->label('Change Summary')
                                ->maxLength(255)
                                ->helperText('Brief description of changes (for version history)')
                                ->visible(fn (): bool => $this->selectedTemplate !== null),
                        ]),
                ])
                ->statePath('data'),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Templat')
                ->action('save')
                ->color('primary'),

            Action::make('preview')
                ->label('Pratonton')
                ->action('preview')
                ->color('warning'),

            Action::make('variables')
                ->label('Lihat Pembolehubah')
                ->action('showVariables')
                ->color('info'),

            Action::make('versionHistory')
                ->label('Sejarah Versi')
                ->action('loadVersionHistory')
                ->color('gray')
                ->visible(fn (): bool => $this->selectedTemplate !== null),
        ];
    }

    public function save(): void
    {
        $data = $this->getFormState();

        $service = app(EmailTemplateService::class);
        $subject = \is_string($data['subject'] ?? null) ? $data['subject'] : '';
        $bodyHtml = \is_string($data['body_html'] ?? null) ? $data['body_html'] : '';
        $validation = $service->validateTemplate($subject, $bodyHtml);

        if (! $validation['valid']) {
            Notification::make()
                ->title('Pengesahan Gagal')
                ->body(implode(', ', $validation['errors']))
                ->danger()
                ->send();

            return;
        }

        // Check for existing template with same category and locale
        /** @var EmailTemplate|null $existing */
        $existing = EmailTemplate::where('category', $data['category'])
            ->where('locale', $data['locale'])
            ->first();

        if ($existing) {
            // Create a new version before updating
            $changeSummary = $data['change_summary'] ?? 'Updated via admin interface';
            $service->createVersion(
                $existing,
                $subject,
                $bodyHtml,
                \is_string($data['body_text'] ?? null) ? $data['body_text'] : null,
                \is_array($data['variables'] ?? null) ? $data['variables'] : null,
                \is_string($changeSummary) ? $changeSummary : 'Updated via admin interface'
            );
            $this->selectedTemplate = $existing->fresh();
            $message = 'Templat dikemaskini berjaya (Versi baru dicipta)';
        } else {
            /** @var EmailTemplate $template */
            $template = EmailTemplate::create([
                'name' => $data['name'],
                'category' => $data['category'],
                'locale' => $data['locale'],
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'body_text' => $data['body_text'] ?? strip_tags($bodyHtml),
                'variables' => $data['variables'] ?? [],
                'is_active' => $data['is_active'] ?? true,
                'current_version' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create initial version
            /** @var EmailTemplateVersion $version */
            $version = EmailTemplateVersion::create([
                'email_template_id' => $template->id,
                'version_number' => 1,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'body_text' => $data['body_text'] ?? strip_tags($bodyHtml),
                'variables' => $data['variables'] ?? [],
                'change_summary' => 'Initial version',
                'created_by' => Auth::id(),
            ]);

            $this->selectedTemplate = $template;
            $message = 'Templat dicipta berjaya';
        }

        // Clear template cache
        $category = \is_string($data['category'] ?? null) ? $data['category'] : null;
        $locale = \is_string($data['locale'] ?? null) ? $data['locale'] : null;
        $service->clearTemplateCache($category, $locale);

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->fillForm($this->selectedTemplate?->toArray());
    }

    public function preview(): void
    {
        $data = $this->getFormState();

        if (empty($data['category'])) {
            Notification::make()
                ->title('Sila pilih kategori terlebih dahulu')
                ->warning()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $template = new EmailTemplate($data);

        $this->previewData = $service->previewTemplate($template);

        Notification::make()
            ->title('Pratonton dijana')
            ->body('Semak bahagian pratonton di bawah')
            ->success()
            ->send();
    }

    public function showVariables(): void
    {
        $data = $this->getFormState();

        if (empty($data['category'])) {
            Notification::make()
                ->title('Sila pilih kategori terlebih dahulu')
                ->warning()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $variables = $service->getAvailableVariables(is_string($data['category'] ?? null) ? $data['category'] : '');

        $variableList = collect($variables)
            ->map(fn ($description, $name) => "{{$name}} - $description")
            ->values()
            ->implode("\n");

        Notification::make()
            ->title('Pembolehubah Tersedia')
            ->body($variableList)
            ->info()
            ->persistent()
            ->send();
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */

    /**
     * @return array<string, mixed>
     */
    public function getExistingTemplates(): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, EmailTemplate> $collection */
        $collection = EmailTemplate::orderBy('category')
            ->orderBy('locale')
            ->get();

        /** @var array<string, array<int, array<string, mixed>>> $templates */
        $templates = $collection
            ->groupBy('category')
            ->map(fn ($templates) => collect($templates)->map(fn (EmailTemplate $template) => $template->toArray())->all())
            ->all();

        return $templates;
    }

    public function loadTemplate(int $templateId): void
    {
        /** @var EmailTemplate|null $template */
        $template = EmailTemplate::find($templateId);

        if ($template) {
            $this->fillForm($template->toArray());
            $this->selectedTemplate = $template;

            Notification::make()
                ->title('Templat dimuatkan')
                ->success()
                ->send();
        }
    }

    public function deleteTemplate(int $templateId): void
    {
        /** @var EmailTemplate|null $template */
        $template = EmailTemplate::find($templateId);

        if ($template) {
            $service = app(EmailTemplateService::class);
            $service->clearTemplateCache($template->category, $template->locale);

            $template->delete();

            Notification::make()
                ->title('Templat dipadam berjaya')
                ->success()
                ->send();
        }
    }

    /**
     * Load version history for the selected template.
     */
    public function loadVersionHistory(): void
    {
        if (! $this->selectedTemplate) {
            Notification::make()
                ->title('Sila pilih templat terlebih dahulu')
                ->warning()
                ->send();

            return;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, EmailTemplateVersion> $versions */
        $versions = $this->selectedTemplate->versions()
            ->with('creator')
            ->orderByDesc('version_number')
            ->get();

        $this->versionHistory = $versions->map(fn (EmailTemplateVersion $version) => [
            'id' => $version->id,
            'version_number' => $version->version_number,
            'subject' => $version->subject,
            'change_summary' => $version->change_summary,
            'created_at' => $version->created_at?->format('d/m/Y H:i'),
            'created_by' => $version->creator?->name ?? 'System',
        ])
            ->toArray();

        $this->showVersionHistory = true;
    }

    /**
     * Restore a specific version of the template.
     */
    public function restoreVersion(int $versionNumber): void
    {
        if (! $this->selectedTemplate) {
            Notification::make()
                ->title('Tiada templat dipilih')
                ->danger()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $result = $service->restoreVersion($this->selectedTemplate, $versionNumber);

        if ($result) {
            /** @var EmailTemplate $refreshed */
            $refreshed = $this->selectedTemplate->refresh();
            $this->selectedTemplate = $refreshed;
            $this->fillForm($this->selectedTemplate->toArray());
            $this->loadVersionHistory();

            Notification::make()
                ->title("Versi {$versionNumber} dipulihkan berjaya")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Gagal memulihkan versi')
                ->danger()
                ->send();
        }
    }

    /**
     * Compare two versions of the template.
     */
    public function compareVersions(): void
    {
        if (! $this->selectedTemplate || ! $this->compareVersion1 || ! $this->compareVersion2) {
            Notification::make()
                ->title('Sila pilih dua versi untuk dibandingkan')
                ->warning()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $this->versionComparison = $service->compareVersions(
            $this->selectedTemplate,
            $this->compareVersion1,
            $this->compareVersion2
        );

        if (isset($this->versionComparison['error'])) {
            Notification::make()
                ->title($this->versionComparison['error'])
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Perbandingan versi dijana')
            ->success()
            ->send();
    }

    /**
     * Preview a specific version.
     */
    public function previewVersion(int $versionNumber): void
    {
        if (! $this->selectedTemplate) {
            return;
        }

        $version = $this->selectedTemplate->getVersion($versionNumber);

        if (! $version) {
            Notification::make()
                ->title('Versi tidak dijumpai')
                ->danger()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $defaultData = $service->getAvailableVariables($this->selectedTemplate->category);

        // Create sample data from available variables
        $sampleData = [];
        foreach ($defaultData as $key => $description) {
            $sampleData[$key] = "[{$key}]";
        }

        $this->previewData = [
            'subject' => $version->renderSubject($sampleData),
            'body_html' => $version->renderBody($sampleData),
            'body_text' => strip_tags($version->renderBody($sampleData)),
            'sample_data' => $sampleData,
            'version' => $versionNumber,
        ];

        Notification::make()
            ->title("Pratonton versi {$versionNumber}")
            ->success()
            ->send();
    }

    /**
     * Close version history panel.
     */
    public function closeVersionHistory(): void
    {
        $this->showVersionHistory = false;
        $this->versionHistory = [];
        $this->versionComparison = [];
        $this->compareVersion1 = null;
        $this->compareVersion2 = null;
    }

    /**
     * Get available versions for comparison dropdown.
     *
     * @return array<int, string>
     */
    public function getVersionOptions(): array
    {
        if (empty($this->versionHistory)) {
            return [];
        }

        $options = [];
        foreach ($this->versionHistory as $version) {
            $options[$version['version_number']] = "Versi {$version['version_number']} - {$version['created_at']}";
        }

        return $options;
    }

    /**
     * @return array<string, mixed>
     */

    /**
     * @return array<string, mixed>
     */
    private function getFormState(): array
    {
        if (property_exists($this, 'form') && is_object($this->form) && method_exists($this->form, 'getState')) {
            $state = $this->form->getState();

            return is_array($state) ? $state : $this->data;
        }

        return $this->data;
    }

    /**
     * @param  array<string, mixed>|null  $state
     */

    /**
     * @param  array<string, mixed>  $state
     */
    private function fillForm(?array $state = null): void
    {
        if (property_exists($this, 'form') && is_object($this->form) && method_exists($this->form, 'fill')) {
            $this->form->fill($state ?? []);

            return;
        }

        $this->data = $state ?? $this->data;
    }
}
