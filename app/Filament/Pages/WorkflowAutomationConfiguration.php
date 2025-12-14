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

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.workflow-automation-configuration';

    /** @var array<string, mixed> */
    public array $data = [];

    public mixed $form = null;

    public ?WorkflowRule $selectedRule = null;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.workflow_automation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.workflow_automation.group');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cipta Peraturan Aliran Kerja Baharu')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Peraturan')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('module')
                                    ->label('Modul')
                                    ->options([
                                        'helpdesk' => 'Helpdesk',
                                        'loans' => 'Pinjaman Aset',
                                        'assets' => 'Pengurusan Aset',
                                    ])
                                    ->required()
                                    ->reactive(),
                            ]),

                        Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(2),

                        Section::make('Syarat (Jika)')
                            ->schema([
                                Repeater::make('conditions')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('field')
                                                    ->label('Medan')
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
                                                        '=' => 'Sama dengan',
                                                        '!=' => 'Tidak sama dengan',
                                                        '>' => 'Lebih besar',
                                                        '<' => 'Kurang daripada',
                                                        '>=' => 'Lebih besar atau sama',
                                                        '<=' => 'Kurang atau sama',
                                                        'contains' => 'Mengandungi',
                                                        'in' => 'Dalam senarai',
                                                    ])
                                                    ->required(),

                                                TextInput::make('value')
                                                    ->label('Nilai')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->addActionLabel('Tambah Syarat')
                                    ->collapsible(),
                            ]),

                        Section::make('Tindakan (Maka)')
                            ->schema([
                                Repeater::make('actions')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('type')
                                                    ->label('Jenis Tindakan')
                                                    ->options([
                                                        'send_email' => 'Hantar E-mel',
                                                        'update_status' => 'Kemaskini Status',
                                                        'assign_user' => 'Tugaskan Pengguna',
                                                        'create_notification' => 'Cipta Pemberitahuan',
                                                    ])
                                                    ->required()
                                                    ->reactive(),

                                                TextInput::make('value')
                                                    ->label('Nilai Tindakan')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->addActionLabel('Tambah Tindakan')
                                    ->collapsible(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('priority')
                                    ->label('Keutamaan')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Nombor lebih tinggi dilaksanakan dahulu'),

                                Toggle::make('is_active')
                                    ->label('Aktif')
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
                ->label('Simpan Peraturan')
                ->action('save')
                ->color('primary'),

            Action::make('test')
                ->label('Test Rules')
                ->form([
                    Select::make('module')
                        ->label('Module')
                        ->options([
                            'helpdesk' => 'Helpdesk',
                            'loans' => 'Asset Loans',
                            'assets' => 'Asset Management',
                        ])
                        ->required(),
                ])
                ->action('testRules')
                ->color('warning'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        WorkflowRule::create($data);

        Notification::make()
            ->title('Peraturan aliran kerja dicipta berjaya')
            ->success()
            ->send();

        $this->form->fill([]);
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
            ->title('Ujian peraturan selesai')
            ->body('Semak log untuk keputusan terperinci')
            ->info()
            ->send();
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getRules(): array
    {
        /** @var array<string, array<int, array<string, mixed>>> $rules */
        $rules = WorkflowRule::orderBy('module')
            ->orderBy('priority', 'desc')
            ->get()
            ->groupBy('module')
            ->map(fn ($group) => collect($group)->map(fn (WorkflowRule $rule) => $rule->toArray())->all())
            ->toArray();

        return $rules;
    }
}
