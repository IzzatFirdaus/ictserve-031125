<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Actions;

use App\Enums\LoanStatus;
use App\Mail\Loans\LoanIssuedMail;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
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
            ->form([
                Section::make('Maklumat Pengeluaran')
                    ->description('Sahkan butiran pengeluaran aset')
                    ->schema([
                        Placeholder::make('applicant_info')
                            ->label('Pemohon')
                            ->content(fn (LoanApplication $record) => $record->applicant_name.' ('.$record->applicant_email.')'),

                        Placeholder::make('application_number')
                            ->label('No. Permohonan')
                            ->content(fn (LoanApplication $record) => $record->application_number),

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
                                $user = auth()->user();

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
                                Placeholder::make('asset_name')
                                    ->label('Aset')
                                    ->content(function ($state, $get) {
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
                                    }),

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

                Section::make('Senarai Semak Aksesori')
                    ->description('Sahkan semua aksesori disertakan')
                    ->schema([
                        CheckboxList::make('accessories')
                            ->label('Aksesori Disertakan')
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

                        Textarea::make('additional_accessories')
                            ->label('Aksesori Tambahan')
                            ->placeholder('Nyatakan aksesori lain yang disertakan')
                            ->rows(2)
                            ->maxLength(500),
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
            ->action(function (LoanApplication $record, array $data) {
                DB::transaction(function () use ($record, $data) {
                    // Create loan transaction
                    $transaction = LoanTransaction::create([
                        'loan_application_id' => $record->id,
                        'transaction_type' => 'issuance',
                        'transaction_date' => $data['issued_at'],
                        'issued_by_name' => $data['issued_by_name'],
                        'issued_by_user_id' => auth()->id(),
                        'condition_on_issue' => 'good', // Default, will be updated per item
                        'accessories_issued' => $data['accessories'] ?? [],
                        'additional_accessories' => $data['additional_accessories'] ?? null,
                        'special_instructions' => $data['special_instructions'] ?? null,
                        'notes' => 'Aset dikeluarkan kepada pemohon',
                    ]);

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
                        'issued_by_user_id' => auth()->id(),
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
