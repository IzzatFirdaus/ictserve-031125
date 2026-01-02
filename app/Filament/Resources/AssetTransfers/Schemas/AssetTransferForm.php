<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetTransfers\Schemas;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * Form Schema untuk Pemindahan Aset
 *
 * Menyediakan borang untuk merekod pemindahan aset dengan:
 * - Pemilihan aset dan pengguna
 * - Status pemindahan dengan medan bersyarat
 * - Catatan dan sebab pembatalan
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 59.1, 59.2, 59.3, 59.4, 59.5
 */
class AssetTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('asset_transfer.sections.transfer_details'))
                    ->schema([
                        // Required Fields
                        Select::make('asset_id')
                            ->label(__('asset_transfer.fields.asset_id'))
                            ->relationship('asset', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Asset $record): string => "{$record->asset_tag} - {$record->name}")
                            ->searchable(['asset_tag', 'name'])
                            ->preload()
                            ->required(),

                        DatePicker::make('transfer_date')
                            ->label(__('asset_transfer.fields.transfer_date'))
                            ->default(now())
                            ->required(),

                        Select::make('status')
                            ->label(__('asset_transfer.fields.status'))
                            ->options([
                                'pending' => __('asset_transfer.status.pending'),
                                'approved' => __('asset_transfer.status.approved'),
                                'completed' => __('asset_transfer.status.completed'),
                                'cancelled' => __('asset_transfer.status.cancelled'),
                            ])
                            ->default('pending')
                            ->required()
                            ->live(),

                        Select::make('to_user_id')
                            ->label(__('asset_transfer.fields.to_user_id'))
                            ->relationship('toUser', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('initiated_by')
                            ->label(__('asset_transfer.fields.initiated_by'))
                            ->relationship('initiator', 'name')
                            ->default(Auth::id())
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('asset_transfer.sections.parties_involved'))
                    ->schema([
                        // Optional Fields
                        Select::make('from_user_id')
                            ->label(__('asset_transfer.fields.from_user_id'))
                            ->relationship('fromUser', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('approved_by')
                            ->label(__('asset_transfer.fields.approved_by'))
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get): bool => in_array($get('status'), ['approved', 'completed']))
                            ->disabled(fn (): bool => ! Auth::user()?->hasAnyRole(['admin', 'superuser']))
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make(__('asset_transfer.sections.location_notes'))
                    ->schema([
                        TextInput::make('from_location')
                            ->label(__('asset_transfer.fields.from_location'))
                            ->maxLength(255)
                            ->nullable(),

                        TextInput::make('to_location')
                            ->label(__('asset_transfer.fields.to_location'))
                            ->maxLength(255)
                            ->nullable(),

                        Textarea::make('notes')
                            ->label(__('asset_transfer.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('cancellation_reason')
                            ->label(__('asset_transfer.fields.cancellation_reason'))
                            ->rows(3)
                            ->visible(fn ($get): bool => $get('status') === 'cancelled')
                            ->required(fn ($get): bool => $get('status') === 'cancelled')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
