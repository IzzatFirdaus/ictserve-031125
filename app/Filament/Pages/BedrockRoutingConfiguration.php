<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Clusters\OllamaAI;
use App\Models\User;
use App\Services\BedrockRoutingConfigurationService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class BedrockRoutingConfiguration extends Page implements HasForms
{
    use InteractsWithForms;

    /** @var array<string, mixed> */
    public array $data = [];

    protected string $view = 'filament.pages.bedrock-routing-configuration';

    protected static ?string $cluster = OllamaAI::class;

    protected static ?int $navigationSort = 6;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-adjustments-horizontal';
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.bedrock_routing.label');
    }

    public function getTitle(): string
    {
        return __('admin_pages.bedrock_routing.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.bedrock_routing.group');
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }

    public function mount(): void
    {
        $service = app(BedrockRoutingConfigurationService::class);
        $this->data = $service->getConfiguration();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin_pages.bedrock_routing.sections.general'))
                    ->description(__('admin_pages.bedrock_routing.sections.general_desc'))
                    ->schema([
                        Toggle::make('enabled')
                            ->label(__('admin_pages.bedrock_routing.fields.enabled'))
                            ->default(false),

                        Toggle::make('prevent_cloud_pii')
                            ->label(__('admin_pages.bedrock_routing.fields.prevent_cloud_pii'))
                            ->default(true),

                        Toggle::make('enforce_malaysia_residency')
                            ->label(__('admin_pages.bedrock_routing.fields.enforce_malaysia_residency'))
                            ->helperText(__('admin_pages.bedrock_routing.fields.enforce_malaysia_residency_help'))
                            ->default(false),
                    ])
                    ->columns(3),

                Section::make(__('admin_pages.bedrock_routing.sections.routing'))
                    ->description(__('admin_pages.bedrock_routing.sections.routing_desc'))
                    ->schema([
                        TextInput::make('routing.cache_ttl_seconds')
                            ->label(__('admin_pages.bedrock_routing.fields.cache_ttl_seconds'))
                            ->numeric()
                            ->minValue(0)
                            ->default(3600)
                            ->required(),

                        TextInput::make('routing.simple_faq_max_words')
                            ->label(__('admin_pages.bedrock_routing.fields.simple_faq_max_words'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(500)
                            ->default(50)
                            ->required(),

                        TextInput::make('routing.max_prompt_chars')
                            ->label(__('admin_pages.bedrock_routing.fields.max_prompt_chars'))
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue(20000)
                            ->default(10000)
                            ->required(),
                    ])
                    ->columns(3),

                Section::make(__('admin_pages.bedrock_routing.sections.rate_limits'))
                    ->description(__('admin_pages.bedrock_routing.sections.rate_limits_desc'))
                    ->schema([
                        Toggle::make('rate_limits.enabled')
                            ->label(__('admin_pages.bedrock_routing.fields.rate_limit_enabled'))
                            ->default(true),

                        TextInput::make('rate_limits.max_attempts_per_minute')
                            ->label(__('admin_pages.bedrock_routing.fields.max_attempts_per_minute'))
                            ->numeric()
                            ->minValue(0)
                            ->default(30)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('admin_pages.bedrock_routing.sections.classification'))
                    ->description(__('admin_pages.bedrock_routing.sections.classification_desc'))
                    ->schema([
                        Toggle::make('classification.require_consent_for_internal')
                            ->label(__('admin_pages.bedrock_routing.fields.require_consent_for_internal'))
                            ->default(true),

                        Toggle::make('classification.block_restricted')
                            ->label(__('admin_pages.bedrock_routing.fields.block_restricted'))
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make(__('admin_pages.bedrock_routing.sections.budgets'))
                    ->description(__('admin_pages.bedrock_routing.sections.budgets_desc'))
                    ->schema([
                        Toggle::make('budgets.enabled')
                            ->label(__('admin_pages.bedrock_routing.fields.budget_enabled'))
                            ->default(false),

                        TextInput::make('budgets.monthly_budget_usd')
                            ->label(__('admin_pages.bedrock_routing.fields.monthly_budget_usd'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Toggle::make('budgets.hard_stop')
                            ->label(__('admin_pages.bedrock_routing.fields.budget_hard_stop'))
                            ->default(false),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('admin_pages.bedrock_routing.actions.save'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('saveConfiguration'),

            Action::make('reset')
                ->label(__('admin_pages.bedrock_routing.actions.reset'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->action('resetToDefaults'),
        ];
    }

    public function saveConfiguration(): void
    {
        try {
            $service = app(BedrockRoutingConfigurationService::class);
            $service->updateConfiguration($this->data);

            Notification::make()
                ->title(__('admin_pages.bedrock_routing.notifications.saved_title'))
                ->body(__('admin_pages.bedrock_routing.notifications.saved_body'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('admin_pages.bedrock_routing.notifications.save_failed_title'))
                ->body(__('admin_pages.bedrock_routing.notifications.save_failed_body'))
                ->danger()
                ->send();
        }
    }

    public function resetToDefaults(): void
    {
        try {
            $service = app(BedrockRoutingConfigurationService::class);
            $service->resetToDefaults();
            $this->data = $service->getConfiguration();

            Notification::make()
                ->title(__('admin_pages.bedrock_routing.notifications.reset_title'))
                ->body(__('admin_pages.bedrock_routing.notifications.reset_body'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('admin_pages.bedrock_routing.notifications.reset_failed_title'))
                ->body(__('admin_pages.bedrock_routing.notifications.reset_failed_body'))
                ->danger()
                ->send();
        }
    }
}
