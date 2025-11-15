<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\ApprovalMatrixService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use UnitEnum;

/**
 * Approval Matrix Configuration Page
 *
 * Superuser-only page for configuring approval matrix rules,
 * grade-based routing, asset value thresholds, and approver assignment logic.
 *
 * @trace Requirements 12.1, 5.1, 5.5
 */
class ApprovalMatrixConfiguration extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected string $view = 'filament.pages.approval-matrix-configuration';

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    public array $matrix = [];

    public array $testResults = [];

    protected ApprovalMatrixService $matrixService;

    public function boot(): void
    {
        $this->matrixService = app(ApprovalMatrixService::class);
        $this->loadMatrix();
    }

    public function loadMatrix(): void
    {
        $this->matrix = $this->matrixService->getApprovalMatrix();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.approval_matrix.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.approval_matrix.group');
    }

    public function getTitle(): string
    {
        return __('admin_pages.approval_matrix.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Repeater::make('matrix.rules')
                    ->label('Peraturan Kelulusan')
                    ->schema([
                        Fieldset::make('Maklumat Asas')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Peraturan')
                                    ->required()
                                    ->maxLength(100),

                                Textarea::make('description')
                                    ->label('Keterangan')
                                    ->rows(2)
                                    ->maxLength(500),

                                TextInput::make('priority')
                                    ->label('Keutamaan')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(100),
                            ])
                            ->columns(2),

                        Fieldset::make('Kriteria Aset')
                            ->schema([
                                TextInput::make('asset_value_min')
                                    ->label('Nilai Minimum (RM)')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('asset_value_max')
                                    ->label('Nilai Maksimum (RM)')
                                    ->numeric()
                                    ->minValue(0),

                                Select::make('asset_categories')
                                    ->label('Kategori Aset')
                                    ->multiple()
                                    ->options([
                                        'laptop' => 'Laptop',
                                        'desktop' => 'Komputer Desktop',
                                        'projector' => 'Projektor',
                                        'printer' => 'Pencetak',
                                        'camera' => 'Kamera',
                                        'audio' => 'Peralatan Audio',
                                        'network' => 'Peralatan Rangkaian',
                                        'other' => 'Lain-lain',
                                    ]),
                            ])
                            ->columns(3),

                        Fieldset::make('Kriteria Pemohon')
                            ->schema([
                                TextInput::make('applicant_grade_min')
                                    ->label('Gred Minimum')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(60),

                                TextInput::make('applicant_grade_max')
                                    ->label('Gred Maksimum')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(60),

                                TextInput::make('duration_days_min')
                                    ->label('Tempoh Minimum (Hari)')
                                    ->numeric()
                                    ->minValue(1),

                                TextInput::make('duration_days_max')
                                    ->label('Tempoh Maksimum (Hari)')
                                    ->numeric()
                                    ->minValue(1),
                            ])
                            ->columns(2),

                        Fieldset::make('Konfigurasi Pelulus')
                            ->schema([
                                Select::make('approver_roles')
                                    ->label('Peranan Pelulus')
                                    ->multiple()
                                    ->options($this->matrixService->getAvailableRoles()),

                                Select::make('approver_grades')
                                    ->label('Gred Pelulus')
                                    ->multiple()
                                    ->options($this->matrixService->getAvailableGrades()),

                                TextInput::make('approval_level')
                                    ->label('Tahap Kelulusan')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(3),

                                Checkbox::make('required')
                                    ->label('Wajib')
                                    ->default(true),

                                Checkbox::make('auto_approve')
                                    ->label('Kelulusan Automatik')
                                    ->default(false),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => $state['name'] ?? 'Peraturan Baharu')
                    ->addActionLabel('Tambah Peraturan')
                    ->reorderableWithButtons()
                    ->cloneable(),
            ])
            ->statePath('matrix');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Konfigurasi')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action(function (): void {
                    try {
                        $this->matrixService->updateApprovalMatrix($this->matrix);

                        Notification::make()
                            ->title('Matriks kelulusan berjaya dikemaskini')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Ralat menyimpan konfigurasi')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('test')
                ->label('Uji Matriks')
                ->icon('heroicon-o-beaker')
                ->color('info')
                ->action(function (): void {
                    $this->runMatrixTests();
                }),

            Action::make('reset')
                ->label('Reset ke Lalai')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset Matriks Kelulusan')
                ->modalDescription('Adakah anda pasti mahu reset matriks kelulusan ke konfigurasi lalai? Semua perubahan akan hilang.')
                ->action(function (): void {
                    $this->matrixService->clearCache();
                    $this->loadMatrix();

                    Notification::make()
                        ->title('Matriks kelulusan telah direset')
                        ->success()
                        ->send();
                }),

            Action::make('export')
                ->label('Eksport')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->action(function () {
                    $export = $this->matrixService->exportMatrix();
                    $filename = 'approval-matrix-'.now()->format('Y-m-d-H-i-s').'.json';

                    return response()->streamDownload(
                        fn () => print (json_encode($export, JSON_PRETTY_PRINT)),
                        $filename,
                        ['Content-Type' => 'application/json']
                    );
                }),

            Action::make('import')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('secondary')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Fail JSON')
                        ->acceptedFileTypes(['application/json'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $content = file_get_contents($data['file']->getRealPath());
                        $importData = json_decode($content, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \Exception('Fail JSON tidak sah');
                        }

                        $this->matrixService->importMatrix($importData);
                        $this->loadMatrix();

                        Notification::make()
                            ->title('Matriks kelulusan berjaya diimport')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Ralat mengimport matriks')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function runMatrixTests(): void
    {
        $testData = [
            [
                'name' => 'Pinjaman Nilai Rendah - Staf Biasa',
                'loan_data' => [
                    'total_value' => 3000,
                    'applicant_grade' => 25,
                    'duration_days' => 14,
                    'asset_categories' => ['laptop'],
                ],
            ],
            [
                'name' => 'Pinjaman Nilai Tinggi - Staf Kanan',
                'loan_data' => [
                    'total_value' => 20000,
                    'applicant_grade' => 45,
                    'duration_days' => 30,
                    'asset_categories' => ['projector'],
                ],
            ],
            [
                'name' => 'Pinjaman Jangka Panjang',
                'loan_data' => [
                    'total_value' => 8000,
                    'applicant_grade' => 30,
                    'duration_days' => 120,
                    'asset_categories' => ['laptop', 'printer'],
                ],
            ],
        ];

        $this->testResults = $this->matrixService->testApprovalMatrix($testData);

        Notification::make()
            ->title('Ujian matriks selesai')
            ->body(count($this->testResults).' ujian telah dijalankan')
            ->info()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }
}
