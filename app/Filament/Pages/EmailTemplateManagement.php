<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class EmailTemplateManagement extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Email Templates';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.email-template-management';

    public ?array $data = [];

    public ?EmailTemplate $selectedTemplate = null;

    public ?array $previewData = null;

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
        $data = $this->form->getState();

        $service = app(EmailTemplateService::class);
        $errors = $service->validateTemplate($data['subject'], $data['body_html']);

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
        $service->clearTemplateCache($data['category'], $data['locale']);

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->form->fill();
    }

    public function preview(): void
    {
        $data = $this->form->getState();

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
        $data = $this->form->getState();

        if (empty($data['category'])) {
            Notification::make()
                ->title('Please select a category first')
                ->warning()
                ->send();

            return;
        }

        $service = app(EmailTemplateService::class);
        $variables = $service->getAvailableVariables($data['category']);

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

    public function getExistingTemplates(): array
    {
        return EmailTemplate::orderBy('category')
            ->orderBy('locale')
            ->get()
            ->groupBy('category')
            ->toArray();
    }

    public function loadTemplate(int $templateId): void
    {
        $template = EmailTemplate::find($templateId);

        if ($template) {
            $this->form->fill($template->toArray());
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
}
