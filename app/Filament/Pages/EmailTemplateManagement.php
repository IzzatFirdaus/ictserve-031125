<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\EmailTemplate;
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

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.email-template-management';

    /** @var array<string, mixed> */
    public array $data = [];

    public ?EmailTemplate $selectedTemplate = null;

    /** @var array<string, mixed> */
    public array $previewData = [];

    public mixed $form = null;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
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
        return __('admin_pages.email_templates.group');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
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
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Template')
                ->action('save')
                ->color('primary'),

            Action::make('preview')
                ->label('Preview')
                ->action('preview')
                ->color('warning'),

            Action::make('variables')
                ->label('Show Variables')
                ->action('showVariables')
                ->color('info'),
        ];
    }

    public function save(): void
    {
        $data = $this->getFormState();

        $service = app(EmailTemplateService::class);
        $subject = is_string($data['subject'] ?? null) ? $data['subject'] : '';
        $bodyHtml = is_string($data['body_html'] ?? null) ? $data['body_html'] : '';
        $errors = $service->validateTemplate($subject, $bodyHtml);

        if (! empty($errors)) {
            Notification::make()
                ->title('Validation failed')
                ->body(implode(', ', $errors))
                ->danger()
                ->send();

            return;
        }

        // Check for existing template with same category and locale
        $existing = EmailTemplate::where('category', $data['category'])
            ->where('locale', $data['locale'])
            ->first();

        if ($existing) {
            $existing->update($data);
            $message = 'Template updated successfully';
        } else {
            EmailTemplate::create($data);
            $message = 'Template created successfully';
        }

        // Clear template cache
        $category = is_string($data['category'] ?? null) ? $data['category'] : null;
        $locale = is_string($data['locale'] ?? null) ? $data['locale'] : null;
        $service->clearTemplateCache($category, $locale);

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->fillForm();
    }

    public function preview(): void
    {
        $data = $this->getFormState();

        if (empty($data['category'])) {
            Notification::make()
                ->title('Please select a category first')
                ->warning()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $template = new EmailTemplate($data);

        $this->previewData = $service->previewTemplate($template);

        Notification::make()
            ->title('Preview generated')
            ->body('Check the preview section below')
            ->success()
            ->send();
    }

    public function showVariables(): void
    {
        $data = $this->getFormState();

        if (empty($data['category'])) {
            Notification::make()
                ->title('Please select a category first')
                ->warning()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $variables = $service->getAvailableVariables(is_string($data['category'] ?? null) ? $data['category'] : '');

        $variableList = collect($variables)
            ->map(fn ($description, $name) => "{{$name}} - $description")
            ->implode("\n");

        Notification::make()
            ->title('Available Variables')
            ->body($variableList)
            ->info()
            ->persistent()
            ->send();
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getExistingTemplates(): array
    {
        /** @var array<string, array<int, array<string, mixed>>> $templates */
        $templates = EmailTemplate::orderBy('category')
            ->orderBy('locale')
            ->get()
            ->groupBy('category')
            ->map(fn ($templates) => collect($templates)->map(fn (EmailTemplate $template) => $template->toArray())->all())
            ->toArray();

        return $templates;
    }

    public function loadTemplate(int $templateId): void
    {
        $template = EmailTemplate::find($templateId);

        if ($template) {
            $this->fillForm($template->toArray());
            $this->selectedTemplate = $template;

            Notification::make()
                ->title('Template loaded')
                ->success()
                ->send();
        }
    }

    public function deleteTemplate(int $templateId): void
    {
        $template = EmailTemplate::find($templateId);

        if ($template) {
            $service = app(EmailTemplateService::class);
            $service->clearTemplateCache($template->category, $template->locale);

            $template->delete();

            Notification::make()
                ->title('Template deleted successfully')
                ->success()
                ->send();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getFormState(): array
    {
        if (is_object($this->form) && method_exists($this->form, 'getState')) {
            $state = $this->form->getState();

            return is_array($state) ? $state : $this->data;
        }

        return $this->data;
    }

    /**
     * @param  array<string, mixed>|null  $state
     */
    private function fillForm(?array $state = null): void
    {
        if (is_object($this->form) && method_exists($this->form, 'fill')) {
            $this->form->fill($state ?? []);
        }
    }
}
