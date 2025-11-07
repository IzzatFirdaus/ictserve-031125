<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Actions;

use App\Enums\LoanStatus;
use App\Mail\Loans\LoanReturnedMail;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Services\HybridHelpdeskService;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProcessReturnAction
{
    public static function make(): Action
    {
        return Action::make('processReturn')
            ->label('Proses Pemulangan')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->color('info')
            ->visible(fn (LoanApplication $record) => in_array($record->status, [LoanStatus::IN_USE, LoanStatus::RETURN_DUE, LoanStatus::OVERDUE]))
            ->modalHeading('Proses Pemulangan Aset')
            ->modalDescription('Sila sahkan butiran pemulangan aset dari peminjam')
            ->modalWidth('3xl')
            ->form([
                Section::make('Maklumat Pemulangan')
                    ->description('Sahkan butiran pemulangan aset')
                    ->schema([
                        Placeholder::make('applicant_info')
                            ->label('Peminjam')
                            ->content(fn (LoanApplication $record) => $record->applicant_name.' ('.$record->applicant_email.')'),

                        Placeholder::make('application_number')
                            ->label('No. Permohonan')
                            ->content(fn (LoanApplication $record) => $record->application_number),

                        DateTimePicker::make('returned_at')
                            ->label('Tarikh & Masa Pemulangan')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        TextInput::make('returned_by_name')
                            ->label('Diterima Oleh')
                            ->default(fn () => auth()->user()->name)
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Penilaian Keadaan Aset')
                    ->description('Rekod keadaan aset selepas pemulangan')
                    ->schema([
                        Repeater::make('asset_conditions')
                            ->label('Keadaan Aset')
                            ->schema([
                                Placeholder::make('asset_name')
                                    ->label('Aset')
                                    ->content(function ($state, $get) {
                                        $loanItemId = $get('../../loan_item_id');
                                        if (! $loanItemId) {
                                            return 'N/A';
                                        }

                                        $loanItem = \App\Models\LoanItem::find($loanItemId);

                                        return $loanItem ? $loanItem->asset->name : 'N/A';
                                    }),

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
                                return $record->loanItems->map(function ($item) {
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

                Section::make('Semakan Aksesori')
                    ->description('Sahkan semua aksesori telah dipulangkan')
                    ->schema([
                        CheckboxList::make('accessories_returned')
                            ->label('Aksesori Dipulangkan')
                            ->options([
                                'power_adapter' => '🔌 Penyesuai Kuasa',
                                'mouse' => '🖱️ Tetikus',
                                'keyboard' => '⌨️ Papan Kekunci',
                                'cable' => '🔗 Kabel',
                                'bag' => '💼 Beg',
                                'manual' => '📖 Manual Pengguna',
                                'warranty_card' => '📄 Kad Waranti',
                            ])
                            ->columns(2)
                            ->gridDirection('row'),

                        Textarea::make('missing_accessories')
                            ->label('Aksesori Hilang')
                            ->placeholder('Nyatakan aksesori yang tidak dipulangkan')
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
                DB::transaction(function () use ($record, $data) {
                    $hasDamagedAssets = false;
                    $damagedAssetDetails = [];

                    // Create loan transaction
                    $transaction = LoanTransaction::create([
                        'loan_application_id' => $record->id,
                        'transaction_type' => 'return',
                        'transaction_date' => $data['returned_at'],
                        'returned_by_name' => $data['returned_by_name'],
                        'returned_by_user_id' => auth()->id(),
                        'condition_on_return' => 'good', // Default, will be updated per item
                        'accessories_returned' => $data['accessories_returned'] ?? [],
                        'missing_accessories' => $data['missing_accessories'] ?? null,
                        'return_notes' => $data['return_notes'] ?? null,
                        'notes' => 'Aset dipulangkan oleh peminjam',
                    ]);

                    // Update loan items with condition assessment
                    if (! empty($data['asset_conditions'])) {
                        foreach ($data['asset_conditions'] as $condition) {
                            if (isset($condition['loan_item_id'])) {
                                $loanItem = \App\Models\LoanItem::find($condition['loan_item_id']);
                                if ($loanItem) {
                                    $loanItem->update([
                                        'condition_on_return' => $condition['condition'],
                                        'condition_notes' => $condition['condition_notes'] ?? null,
                                        'damage_description' => $condition['damage_description'] ?? null,
                                    ]);

                                    // Check if asset is damaged
                                    if (in_array($condition['condition'], ['poor', 'damaged'])) {
                                        $hasDamagedAssets = true;
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
                                    } else {
                                        // Update asset status to available
                                        $loanItem->asset->update([
                                            'status' => 'available',
                                            'availability' => 'available',
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    // Update loan application status
                    $record->update([
                        'status' => LoanStatus::COMPLETED,
                        'returned_at' => $data['returned_at'],
                        'returned_by_name' => $data['returned_by_name'],
                        'returned_by_user_id' => auth()->id(),
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
                                'user_id' => auth()->id(),
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
