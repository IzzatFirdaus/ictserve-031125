<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Actions;

use App\Enums\LoanStatus;
use App\Enums\TransactionType;
use App\Mail\Loans\LoanReturnedMail;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Services\AccessoryTrackingService;
use App\Services\HybridHelpdeskService;
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
 * ProcessReturnAction - v3.5.0 True Hybrid Architecture
 *
 * Handles asset check-in with accessory tracking and discrepancy detection
 * per PK.(S).MOTAC.07.(L3).
 *
 * @see D03 SRS-LOAN-007 - Check-in Transaction Recording
 * @see Requirements 6.2, 6.3, 26.4, 26.5
 */
class ProcessReturnAction
{
    public static function make(): Action
    {
        return Action::make('processReturn')
            ->label(__('filament.actions.process_return'))
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->color('info')
            ->visible(fn (LoanApplication $record) => in_array($record->status, [LoanStatus::IN_USE, LoanStatus::RETURN_DUE, LoanStatus::OVERDUE]))
            ->modalHeading('Proses Pemulangan Aset')
            ->modalDescription('Sila sahkan butiran pemulangan aset dari peminjam')
            ->modalWidth('3xl')
            ->fillForm([
                Section::make('Maklumat Pemulangan')
                    ->description('Sahkan butiran pemulangan aset')
                    ->schema([
                        TextInput::make('applicant_info')
                            ->label(__('filament.return.borrower'))
                            ->default(fn (LoanApplication $record) => $record->applicant_name.' ('.$record->applicant_email.')')
                            ->disabled(),

                        TextInput::make('application_number')
                            ->label(__('filament.return.application_number'))
                            ->default(fn (LoanApplication $record) => $record->application_number)
                            ->disabled(),

                        DateTimePicker::make('returned_at')
                            ->label(__('filament.return.return_datetime'))
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        TextInput::make('returned_by_name')
                            ->label('Diterima Oleh')
                            ->default(function (): string {
                                $user = Auth::user();

                                return $user ? $user->name : 'System';
                            })
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Penilaian Keadaan Aset')
                    ->description('Rekod keadaan aset selepas pemulangan')
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
                                        'poor' => '❌ Kurang Baik - Berfungsi tetapi memerlukan perhatian',
                                        'damaged' => '🔴 Rosak - Memerlukan pembaikan segera',
                                    ])
                                    ->required()
                                    ->default('good')
                                    ->native(false)
                                    ->live(),

                                Textarea::make('condition_notes')
                                    ->label('Catatan Keadaan')
                                    ->placeholder('Contoh: Skrin bersih, tiada calar, semua port berfungsi')
                                    ->rows(2)
                                    ->maxLength(500),

                                Textarea::make('damage_description')
                                    ->label('Penerangan Kerosakan')
                                    ->placeholder('Nyatakan kerosakan dengan terperinci')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->visible(fn ($get) => in_array($get('condition'), ['poor', 'damaged']))
                                    ->required(fn ($get) => in_array($get('condition'), ['poor', 'damaged'])),

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
                                        'damage_description' => '',
                                    ];
                                })->toArray();
                            })
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(1),
                    ]),

                // Accessory Tracking Section - Requirements 26.4, 26.5
                Section::make('Semakan Aksesori / Accessory Verification')
                    ->description('Sahkan semua aksesori telah dipulangkan - Senarai dipra-isi dari rekod pengeluaran')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Textarea::make('checkout_accessories_info')
                            ->label('Aksesori Dikeluarkan Semasa Check-out')
                            ->default(function (LoanApplication $record): string {
                                $accessoryService = app(AccessoryTrackingService::class);
                                $checkoutAccessories = $accessoryService->getCheckoutAccessoriesForLoan($record->id);

                                if ($checkoutAccessories->isEmpty()) {
                                    return 'Tiada rekod aksesori semasa pengeluaran';
                                }

                                $lines = [];
                                foreach ($checkoutAccessories as $accessory) {
                                    $icon = $accessory->present_at_checkout ? '✅' : '❌';
                                    $name = $accessory->accessory_type === 'OTHERS'
                                        ? $accessory->accessory_name
                                        : match ($accessory->accessory_type) {
                                            'POWER_ADAPTER' => 'Penyesuai Kuasa',
                                            'BAG' => 'Beg',
                                            'MOUSE' => 'Tetikus',
                                            'USB_CABLE' => 'Kabel USB',
                                            'HDMI_VGA_CABLE' => 'Kabel HDMI/VGA',
                                            'REMOTE' => 'Alat Kawalan Jauh',
                                            default => $accessory->accessory_type,
                                        };
                                    $lines[] = "{$icon} {$name}";
                                }

                                return implode("\n", $lines);
                            })
                            ->disabled()
                            ->rows(4),

                        Repeater::make('accessory_checklist')
                            ->label('Semakan Aksesori Dipulangkan / Returned Accessories Check')
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
                                    ->disabled()
                                    ->live(),

                                TextInput::make('accessory_name')
                                    ->label('Nama Aksesori')
                                    ->disabled()
                                    ->visible(fn ($get) => $get('accessory_type') === 'OTHERS'),

                                TextInput::make('checkout_status')
                                    ->label('Status Semasa Pengeluaran')
                                    ->default(fn ($get) => $get('was_present_at_checkout')
                                        ? '✅ Disertakan semasa pengeluaran'
                                        : '❌ Tidak disertakan semasa pengeluaran')
                                    ->disabled(),

                                Toggle::make('present')
                                    ->label('Dipulangkan / Returned')
                                    ->default(true)
                                    ->inline(false)
                                    ->live(),

                                // Discrepancy warning - Requirement 26.5
                                TextInput::make('discrepancy_warning')
                                    ->label('Amaran')
                                    ->default('⚠️ PERHATIAN: Aksesori ini disertakan semasa pengeluaran tetapi tidak dipulangkan!')
                                    ->visible(fn ($get) => $get('was_present_at_checkout') && ! $get('present'))
                                    ->disabled()
                                    ->extraAttributes(['class' => 'text-red-600 font-semibold']),

                                Textarea::make('condition_notes')
                                    ->label('Catatan Keadaan / Condition Notes')
                                    ->placeholder('Nyatakan sebarang perubahan keadaan')
                                    ->rows(2)
                                    ->maxLength(500),

                                TextInput::make('was_present_at_checkout')
                                    ->hidden()
                                    ->dehydrated(),
                            ])
                            ->default(function (LoanApplication $record): array {
                                $accessoryService = app(AccessoryTrackingService::class);
                                $checkoutAccessories = $accessoryService->getCheckoutAccessoriesForLoan($record->id);

                                if ($checkoutAccessories->isEmpty()) {
                                    // Return default accessories if no checkout record
                                    return [
                                        ['accessory_type' => 'POWER_ADAPTER', 'present' => false, 'was_present_at_checkout' => false],
                                        ['accessory_type' => 'BAG', 'present' => false, 'was_present_at_checkout' => false],
                                        ['accessory_type' => 'MOUSE', 'present' => false, 'was_present_at_checkout' => false],
                                        ['accessory_type' => 'USB_CABLE', 'present' => false, 'was_present_at_checkout' => false],
                                        ['accessory_type' => 'HDMI_VGA_CABLE', 'present' => false, 'was_present_at_checkout' => false],
                                        ['accessory_type' => 'REMOTE', 'present' => false, 'was_present_at_checkout' => false],
                                    ];
                                }

                                // Pre-populate from checkout data - Requirement 26.4
                                return $checkoutAccessories->map(fn ($accessory): array => [
                                    'accessory_type' => $accessory->accessory_type,
                                    'accessory_name' => $accessory->accessory_name,
                                    'present' => (bool) $accessory->present_at_checkout, // Default to same as checkout
                                    'was_present_at_checkout' => (bool) $accessory->present_at_checkout,
                                    'condition_notes' => '',
                                ])->toArray();
                            })
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(1),

                        Textarea::make('missing_accessories_notes')
                            ->label('Catatan Aksesori Hilang / Missing Accessories Notes')
                            ->placeholder('Nyatakan butiran aksesori yang tidak dipulangkan dan tindakan yang diambil')
                            ->rows(2)
                            ->maxLength(500),
                    ]),

                Section::make('Catatan Tambahan')
                    ->schema([
                        Textarea::make('return_notes')
                            ->label('Catatan Pemulangan')
                            ->placeholder('Contoh: Aset dipulangkan dalam keadaan baik')
                            ->rows(3)
                            ->maxLength(1000),

                        Checkbox::make('confirm_return')
                            ->label('Saya mengesahkan bahawa semua butiran adalah tepat dan aset telah diterima')
                            ->required()
                            ->accepted(),
                    ]),
            ])
            ->action(function (LoanApplication $record, array $data) {
                $accessoryService = app(AccessoryTrackingService::class);

                DB::transaction(function () use ($record, $data, $accessoryService) {
                    $hasDamagedAssets = false;
                    $damagedAssetDetails = [];

                    // Create loan transaction with v3.5.0 fields
                    $transaction = LoanTransaction::create([
                        'loan_application_id' => $record->id,
                        'transaction_type' => TransactionType::RETURN,
                        'performed_by_admin_id' => Auth::id(),
                        'performed_at' => $data['returned_at'],
                        'condition_notes' => $data['return_notes'] ?? null,
                        'damage_reported' => false, // Will be updated if damage found
                        'notes' => 'Aset dipulangkan oleh peminjam',
                    ]);

                    // Record accessories using AccessoryTrackingService - Requirements 26.4, 26.6
                    $accessoryData = collect($data['accessory_checklist'] ?? [])
                        ->map(fn (array $item): array => [
                            'accessory_type' => $item['accessory_type'],
                            'accessory_name' => $item['accessory_name'] ?? null,
                            'present' => (bool) ($item['present'] ?? false),
                            'condition_notes' => $item['condition_notes'] ?? null,
                        ])
                        ->toArray();

                    $accessoryService->recordCheckinAccessories($transaction, $accessoryData);

                    // Check for accessory discrepancies - Requirement 26.5
                    $checkoutTransaction = LoanTransaction::where('loan_application_id', $record->id)
                        ->where('transaction_type', TransactionType::ISSUE)
                        ->latest('performed_at')
                        ->first();

                    $hasAccessoryDiscrepancies = false;
                    if ($checkoutTransaction) {
                        $hasAccessoryDiscrepancies = $accessoryService->hasDiscrepancies($checkoutTransaction, $transaction);
                    }

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
                            'condition_on_return' => $condition['condition'] ?? 'good',
                            'condition_notes' => $condition['condition_notes'] ?? null,
                            'damage_description' => $condition['damage_description'] ?? null,
                        ]);

                        // Check if asset is damaged
                        if (in_array($condition['condition'] ?? '', ['poor', 'damaged'], true)) {
                            $hasDamagedAssets = true;

                            if ($loanItem->asset) {
                                $damagedAssetDetails[] = [
                                    'asset' => $loanItem->asset,
                                    'condition' => $condition['condition'],
                                    'description' => $condition['damage_description'] ?? 'Tiada penerangan',
                                ];

                                // Update asset status to maintenance
                                $loanItem->asset->update([
                                    'status' => 'maintenance',
                                    'availability' => 'maintenance',
                                ]);
                            }
                        } elseif ($loanItem->asset) {
                            // Update asset status to available
                            $loanItem->asset->update([
                                'status' => 'available',
                                'availability' => 'available',
                            ]);
                        }
                    }

                    // Update loan application status
                    $record->update([
                        'status' => LoanStatus::COMPLETED,
                        'returned_at' => $data['returned_at'],
                        'returned_by_name' => $data['returned_by_name'],
                        'returned_by_user_id' => Auth::id(),
                    ]);

                    // Create maintenance tickets for damaged assets (5-second SLA)
                    if ($hasDamagedAssets) {
                        $helpdeskService = app(HybridHelpdeskService::class);

                        foreach ($damagedAssetDetails as $detail) {
                            $ticket = $helpdeskService->createTicket([
                                'title' => "Kerosakan Aset: {$detail['asset']->name}",
                                'description' => "Aset rosak semasa pemulangan pinjaman.\n\nNo. Permohonan: {$record->application_number}\nPeminjam: {$record->applicant_name}\n\nKeadaan: ".ucfirst($detail['condition'])."\n\nPenerangan Kerosakan:\n{$detail['description']}",
                                'priority' => 'high',
                                'category' => 'asset_damage',
                                'damage_type' => $detail['condition'],
                                'asset_id' => $detail['asset']->id,
                                'is_guest' => false,
                                'user_id' => Auth::id(),
                            ]);

                            // Link ticket to loan application
                            \App\Models\CrossModuleIntegration::create([
                                'source_module' => 'helpdesk',
                                'source_id' => $ticket->id,
                                'target_module' => 'asset_loan',
                                'target_id' => $record->id,
                                'integration_type' => 'damage_report',
                                'metadata' => [
                                    'damage_type' => $detail['condition'],
                                    'reported_at' => now()->toIso8601String(),
                                    'asset_id' => $detail['asset']->id,
                                ],
                            ]);
                        }
                    }

                    // Send email notification
                    Mail::to($record->applicant_email)
                        ->queue(new LoanReturnedMail($record, $transaction, $hasDamagedAssets));
                });

                Notification::make()
                    ->success()
                    ->title('Pemulangan Berjaya')
                    ->body("Aset telah dipulangkan oleh {$record->applicant_name}")
                    ->send();
            })
            ->successNotificationTitle('Pemulangan aset berjaya diproses')
            ->requiresConfirmation()
            ->modalSubmitActionLabel('Proses Pemulangan')
            ->modalCancelActionLabel('Batal');
    }
}
