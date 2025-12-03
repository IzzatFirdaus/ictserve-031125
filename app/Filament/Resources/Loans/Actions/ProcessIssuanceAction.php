<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Actions;

use App\Enums\LoanStatus;
use App\Enums\TransactionType;
use App\Mail\Loans\LoanIssuedMail;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Services\AccessoryTrackingService;
use App\Services\OTPHandoverService;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * ProcessIssuanceAction - v3.5.0 True Hybrid Architecture
 *
 * Handles asset check-out with accessory tracking per PK.(S).MOTAC.07.(L3).
 *
 * @see D03 SRS-LOAN-007 - Check-out Transaction Recording
 * @see Requirements 6.1, 26.1, 26.2, 26.3
 */
class ProcessIssuanceAction
{
    public static function make(): Action
    {
        return Action::make('processIssuance')
            ->label('Proses Pengeluaran')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->visible(fn (LoanApplication $record) => $record->status === LoanStatus::APPROVED)
            ->modalHeading('Proses Pengeluaran Aset')
            ->modalDescription('Sila sahkan butiran pengeluaran aset kepada pemohon')
            ->modalWidth('3xl')
            ->schema([
                Section::make('Pengesahan OTP')
                    ->description('Sila minta pemohon memberikan kod OTP 4-digit untuk pengesahan')
                    ->schema([
                        TextInput::make('otp_code')
                            ->label('Kod OTP')
                            ->required()
                            ->length(4)
                            ->numeric()
                            ->password()
                            ->revealable()
                            ->helperText('Kod OTP 4-digit yang dihantar ke emel pemohon')
                            ->validationAttribute('OTP Code'),
                    ]),

                Section::make('Maklumat Pengeluaran')
                    ->description('Sahkan butiran pengeluaran aset')
                    ->schema([
                        TextInput::make('applicant_info')
                            ->label('Pemohon')
                            ->default(fn (LoanApplication $record) => $record->applicant_name.' ('.$record->applicant_email.')')
                            ->disabled(),

                        TextInput::make('application_number')
                            ->label('No. Permohonan')
                            ->default(fn (LoanApplication $record) => $record->application_number)
                            ->disabled(),

                        DateTimePicker::make('issued_at')
                            ->label('Tarikh & Masa Pengeluaran')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        TextInput::make('issued_by_name')
                            ->label('Dikeluarkan Oleh')
                            ->default(function (): string {
                                $user = Auth::user();

                                return $user ? $user->name : 'System';
                            })
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Penilaian Keadaan Aset')
                    ->description('Rekod keadaan aset sebelum pengeluaran')
                    ->schema([
                        Repeater::make('asset_conditions')
                            ->label('Keadaan Aset')
                            ->schema([
                                TextInput::make('asset_name')
                                    ->label('Aset')
                                    ->default(function ($state, $get) {
                                        $loanItemId = $get('../../loan_item_id');
                                        if (! $loanItemId) {
                                            return 'N/A';
                                        }

                                        /** @var LoanItem|null $loanItem */
                                        $loanItem = LoanItem::find($loanItemId);

                                        if ($loanItem === null || $loanItem->asset === null) {
                                            return 'N/A';
                                        }

                                        return $loanItem->asset->name;
                                    })
                                    ->disabled(),

                                Select::make('condition')
                                    ->label('Keadaan')
                                    ->options([
                                        'excellent' => '⭐ Cemerlang - Seperti baru',
                                        'good' => '✅ Baik - Berfungsi dengan sempurna',
                                        'fair' => '⚠️ Sederhana - Berfungsi dengan sedikit kesan penggunaan',
                                    ])
                                    ->required()
                                    ->default('good')
                                    ->native(false),

                                Textarea::make('condition_notes')
                                    ->label('Catatan Keadaan')
                                    ->placeholder('Contoh: Skrin bersih, tiada calar, semua port berfungsi')
                                    ->rows(2)
                                    ->maxLength(500),

                                TextInput::make('loan_item_id')
                                    ->hidden()
                                    ->dehydrated(),
                            ])
                            ->default(function (LoanApplication $record) {
                                return $record->loanItems->map(function ($item): array {
                                    /** @var LoanItem $item */
                                    return [
                                        'loan_item_id' => $item->id,
                                        'condition' => 'good',
                                        'condition_notes' => '',
                                    ];
                                })->toArray();
                            })
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(1),
                    ]),

                // Accessory Tracking Section - Requirements 26.1, 26.2, 26.3
                Section::make('Senarai Semak Aksesori / Accessory Checklist')
                    ->description('Sahkan semua aksesori yang disertakan mengikut PK.(S).MOTAC.07.(L3)')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Repeater::make('accessory_checklist')
                            ->label('Aksesori Standard / Standard Accessories')
                            ->schema([
                                Select::make('accessory_type')
                                    ->label('Jenis Aksesori')
                                    ->options([
                                        'POWER_ADAPTER' => '🔌 Penyesuai Kuasa / Power Adapter',
                                        'BAG' => '💼 Beg / Bag',
                                        'MOUSE' => '🖱️ Tetikus / Mouse',
                                        'USB_CABLE' => '🔗 Kabel USB / USB Cable',
                                        'HDMI_VGA_CABLE' => '📺 Kabel HDMI/VGA / HDMI/VGA Cable',
                                        'REMOTE' => '📱 Alat Kawalan Jauh / Remote',
                                        'OTHERS' => '📦 Lain-lain / Others',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                TextInput::make('accessory_name')
                                    ->label('Nama Aksesori (untuk Lain-lain)')
                                    ->placeholder('Nyatakan nama aksesori')
                                    ->maxLength(100)
                                    ->visible(fn ($get) => $get('accessory_type') === 'OTHERS')
                                    ->required(fn ($get) => $get('accessory_type') === 'OTHERS'),

                                Toggle::make('present')
                                    ->label('Disertakan / Included')
                                    ->default(true)
                                    ->inline(false),

                                Textarea::make('condition_notes')
                                    ->label('Catatan Keadaan / Condition Notes')
                                    ->placeholder('Contoh: Berfungsi dengan baik, tiada kerosakan')
                                    ->rows(2)
                                    ->maxLength(500),
                            ])
                            ->default([
                                ['accessory_type' => 'POWER_ADAPTER', 'present' => true, 'condition_notes' => ''],
                                ['accessory_type' => 'BAG', 'present' => false, 'condition_notes' => ''],
                                ['accessory_type' => 'MOUSE', 'present' => false, 'condition_notes' => ''],
                                ['accessory_type' => 'USB_CABLE', 'present' => false, 'condition_notes' => ''],
                                ['accessory_type' => 'HDMI_VGA_CABLE', 'present' => false, 'condition_notes' => ''],
                                ['accessory_type' => 'REMOTE', 'present' => false, 'condition_notes' => ''],
                            ])
                            ->addActionLabel('Tambah Aksesori Lain / Add Other Accessory')
                            ->reorderable(false)
                            ->columns(1),
                    ]),

                Section::make('Arahan Khas')
                    ->schema([
                        Textarea::make('special_instructions')
                            ->label('Arahan Khas')
                            ->placeholder('Contoh: Aset mesti dikembalikan dalam keadaan bersih')
                            ->rows(3)
                            ->maxLength(1000),

                        Checkbox::make('confirm_issuance')
                            ->label('Saya mengesahkan bahawa semua butiran adalah tepat dan aset telah diserahkan kepada pemohon')
                            ->required()
                            ->accepted(),
                    ]),
            ])
            ->action(function (LoanApplication $record, array $data, OTPHandoverService $otpService, AccessoryTrackingService $accessoryService, Action $action) {
                // Verify OTP
                if (! $otpService->validatePickupOTP($record, $data['otp_code'])) {
                    Notification::make()
                        ->danger()
                        ->title('Pengesahan Gagal')
                        ->body('Kod OTP tidak sah atau telah tamat tempoh.')
                        ->send();

                    $action->halt();
                }

                DB::transaction(function () use ($record, $data, $accessoryService) {
                    // Create loan transaction with v3.5.0 fields
                    $transaction = LoanTransaction::create([
                        'loan_application_id' => $record->id,
                        'transaction_type' => TransactionType::ISSUE,
                        'performed_by_admin_id' => Auth::id(),
                        'performed_at' => $data['issued_at'],
                        'condition_notes' => $data['special_instructions'] ?? null,
                        'damage_reported' => false,
                        'notes' => 'Aset dikeluarkan kepada pemohon',
                    ]);

                    // Record accessories using AccessoryTrackingService - Requirements 26.2, 26.6
                    $accessoryData = collect($data['accessory_checklist'] ?? [])
                        ->map(fn (array $item): array => [
                            'accessory_type' => $item['accessory_type'],
                            'accessory_name' => $item['accessory_name'] ?? null,
                            'present' => (bool) ($item['present'] ?? false),
                            'condition_notes' => $item['condition_notes'] ?? null,
                        ])
                        ->toArray();

                    $accessoryService->recordCheckoutAccessories($transaction, $accessoryData);

                    // Update loan items with condition assessment
                    $assetConditions = $data['asset_conditions'] ?? [];
                    foreach ($assetConditions as $condition) {
                        if (! is_array($condition) || ! isset($condition['loan_item_id'])) {
                            continue;
                        }

                        /** @var LoanItem|null $loanItem */
                        $loanItem = LoanItem::find($condition['loan_item_id']);
                        if ($loanItem === null) {
                            continue;
                        }

                        $loanItem->update([
                            'condition_on_issue' => $condition['condition'] ?? 'good',
                            'condition_notes' => $condition['condition_notes'] ?? null,
                        ]);

                        // Update asset status to 'on_loan'
                        if ($loanItem->asset) {
                            $loanItem->asset->update([
                                'status' => 'on_loan',
                                'availability' => 'on_loan',
                            ]);
                        }
                    }

                    // Update loan application status
                    $record->update([
                        'status' => LoanStatus::IN_USE,
                        'issued_at' => $data['issued_at'],
                        'issued_by_name' => $data['issued_by_name'],
                        'issued_by_user_id' => Auth::id(),
                    ]);

                    // Send email notification
                    Mail::to($record->applicant_email)
                        ->queue(new LoanIssuedMail($record, $transaction));
                });

                Notification::make()
                    ->success()
                    ->title('Pengeluaran Berjaya')
                    ->body("Aset telah dikeluarkan kepada {$record->applicant_name}")
                    ->send();
            })
            ->successNotificationTitle('Pengeluaran aset berjaya diproses')
            ->requiresConfirmation()
            ->modalSubmitActionLabel('Proses Pengeluaran')
            ->modalCancelActionLabel('Batal');
    }
}
