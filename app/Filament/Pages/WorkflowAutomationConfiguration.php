<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\WorkflowRule;
use App\Services\WorkflowAutomationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
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

class WorkflowAutomationConfiguration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Workflow Automation';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.workflow-automation-configuration';

    public ?array $data = [];

    public ?WorkflowRule $selectedRule = null;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Create New Workflow Rule')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Rule Name')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('module')
                                    ->label('Module')
                                    ->options([
                                        'helpdesk' => 'Helpdesk',
                                        'loans' => 'Asset Loans',
                                        'assets' => 'Asset Management',
                                    ])
                                    ->required()
                                    ->reactive(),
                            ]),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2),

                        Section::make('Conditions (If)')
                            ->schema([
                                Repeater::make('conditions')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('field')
                                                    ->label('Field')
                                                    ->options(function (callable $get) {
                                                        $module = $get('../../module');
                                                        if (! $module) {
                                                            return [];
                                                        }

                                                        $service = app(WorkflowAutomationService::class);

                                                        return $service->getAvailableConditions($module);
                                                    })
                                                    ->required(),

                                                Select::make('operator')
                                                    ->label('Operator')
                                                    ->options([
                                                        '=' => 'Equals',
                                                        '!=' => 'Not Equals',
                                                        '>' => 'Greater Than',
                                                        '<' => 'Less Than',
                                                        '>=' => 'Greater Than or Equal',
                                                        '<=' => 'Less Than or Equal',
                                                        'contains' => 'Contains',
                                                        'in' => 'In List',
                                                    ])
                                                    ->required(),

                                                TextInput::make('value')
                                                    ->label('Value')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->addActionLabel('Add Condition')
                                    ->collapsible(),
                            ]),

                        Section::make('Actions (Then)')
                            ->schema([
                                Repeater::make('actions')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('type')
                                                    ->label('Action Type')
                                                    ->options([
                                                        'send_email' => 'Send Email',
                                                        'update_status' => 'Update Status',
                                                        'assign_user' => 'Assign User',
                                                        'create_notification' => 'Create Notification',
                                                    ])
                                                    ->required()
                                                    ->reactive(),

                                                TextInput::make('value')
                                                    ->label('Action Value')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->addActionLabel('Add Action')
                                    ->collapsible(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('priority')
                                    ->label('Priority')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Higher numbers execute first'),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Rule')
                ->action('save')
                ->color('primary'),

            Action::make('test')
                ->label('Test Rules')
                ->action('testRules')
                ->color('warning'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        WorkflowRule::create($data);

        Notification::make()
            ->title('Workflow rule created successfully')
            ->success()
            ->send();

        $this->form->fill();
    }

    public function testRules(): void
    {
        $sampleData = [
            'helpdesk' => [
                ['priority' => 'urgent', 'status' => 'open', 'created_hours_ago' => 2],
                ['priority' => 'low', 'status' => 'assigned', 'created_hours_ago' => 48],
            ],
            'loans' => [
                ['status' => 'pending', 'asset_value' => 5000, 'applicant_grade' => 45],
                ['status' => 'approved', 'asset_value' => 1000, 'applicant_grade' => 38],
            ],
            'assets' => [
                ['status' => 'maintenance', 'condition' => 'damaged'],
                ['status' => 'available', 'condition' => 'excellent'],
            ],
        ];

        $service = app(WorkflowAutomationService::class);
        $rules = WorkflowRule::active()->get();

        $results = [];
        foreach ($rules as $rule) {
            $moduleData = $sampleData[$rule->module] ?? [];
            $results[$rule->name] = $service->testRule($rule, $moduleData);
        }

        Notification::make()
            ->title('Rule testing completed')
            ->body('Check logs for detailed results')
            ->info()
            ->send();
    }

    public function getRules(): array
    {
        return WorkflowRule::orderBy('module')
            ->orderBy('priority', 'desc')
            ->get()
            ->groupBy('module')
            ->toArray();
    }
}
