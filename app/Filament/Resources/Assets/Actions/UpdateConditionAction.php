<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Actions;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;

/**
 * Update Asset Condition Action
 *
 * Allows admins to update asset condition with notes and automatic status updates.
 *
 * @trace Requirements 3.3
 */
class UpdateConditionAction
{
    public static function make(): Action
    {
        return Action::make('updateCondition')
            ->label('Kemaskini Keadaan')
            ->icon('heroicon-o-wrench-screwdriver')
            ->color('warning')
            ->form([
                Section::make('Penilaian Keadaan Aset')
                    ->description('Kemaskini keadaan semasa aset dan tambah nota jika perlu')
                    ->schema([
                        Select::make('condition')
                            ->label('Keadaan Aset')
                            ->options([
                                AssetCondition::EXCELLENT->value => 'Cemerlang - Seperti baharu',
                                AssetCondition::GOOD->value => 'Baik - Berfungsi dengan sempurna',
                                AssetCondition::FAIR->value => 'Sederhana - Berfungsi tetapi ada tanda penggunaan',
                                AssetCondition::POOR->value => 'Lemah - Memerlukan perhatian',
                                AssetCondition::DAMAGED->value => 'Rosak - Memerlukan pembaikan segera',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-set status based on condition
                                if (in_array($state, [AssetCondition::DAMAGED->value, AssetCondition::POOR->value])) {
                                    $set('status', AssetStatus::MAINTENANCE->value);
                                } elseif ($state === AssetCondition::GOOD->value || $state === AssetCondition::EXCELLENT->value) {
                                    $set('status', AssetStatus::AVAILABLE->value);
                                }
                            }),

                        Select::make('status')
                            ->label('Status Aset')
                            ->options([
                                AssetStatus::AVAILABLE->value => 'Tersedia',
                                AssetStatus::LOANED->value => 'Dipinjam',
                                AssetStatus::MAINTENANCE->value => 'Penyelenggaraan',
                                AssetStatus::RETIRED->value => 'Bersara',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText('Status akan dikemaskini secara automatik berdasarkan keadaan'),

                        Textarea::make('condition_notes')
                            ->label('Nota Keadaan')
                            ->placeholder('Nyatakan sebarang kerosakan, isu, atau pemerhatian...')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Maksimum 1000 aksara'),

                        DatePicker::make('next_maintenance_date')
                            ->label('Tarikh Penyelenggaraan Seterusnya')
                            ->native(false)
                            ->minDate(now())
                            ->helperText('Tetapkan tarikh penyelenggaraan jika diperlukan')
                            ->visible(fn (callable $get) => in_array($get('condition'), [
                                AssetCondition::FAIR->value,
                                AssetCondition::POOR->value,
                                AssetCondition::DAMAGED->value,
                            ])),

                        Toggle::make('create_maintenance_ticket')
                            ->label('Cipta Tiket Penyelenggaraan')
                            ->helperText('Cipta tiket penyelenggaraan secara automatik untuk aset ini')
                            ->default(false)
                            ->visible(fn (callable $get) => in_array($get('condition'), [
                                AssetCondition::POOR->value,
                                AssetCondition::DAMAGED->value,
                            ])),
                    ]),
            ])
            ->action(function (Model $record, array $data): void {
                // Update asset condition and status
                $record->update([
                    'condition' => $data['condition'],
                    'status' => $data['status'],
                    'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
                ]);

                // Create maintenance ticket if requested
                if (($data['create_maintenance_ticket'] ?? false) && isset($data['condition_notes'])) {
                    $ticketData = [
                        'title' => "Penyelenggaraan Aset: {$record->name}",
                        'description' => $data['condition_notes'],
                        'category' => 'maintenance',
                        'priority' => $data['condition'] === AssetCondition::DAMAGED->value ? 'urgent' : 'high',
                        'asset_id' => $record->id,
                        'status' => 'open',
                        'is_guest' => false,
                    ];

                    app(\App\Services\HybridHelpdeskService::class)->createTicket($ticketData);
                }

                // Send success notification
                Notification::make()
                    ->success()
                    ->title('Keadaan Aset Dikemaskini')
                    ->body("Keadaan aset {$record->name} telah dikemaskini kepada ".ucfirst(str_replace('_', ' ', $data['condition'])).'.')
                    ->send();
            })
            ->requiresConfirmation()
            ->modalHeading('Kemaskini Keadaan Aset')
            ->modalDescription('Kemaskini keadaan semasa aset dan status berkaitan')
            ->modalSubmitActionLabel('Kemaskini')
            ->modalWidth('2xl');
    }
}
