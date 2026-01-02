<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetTransfers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Infolist Schema untuk Pemindahan Aset
 *
 * Menyediakan paparan maklumat pemindahan aset dengan:
 * - Butiran pemindahan (aset, tarikh, status)
 * - Pihak terlibat (pengguna asal/baharu, pemula, pelulus)
 * - Lokasi dan catatan
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 63.1, 63.2, 63.3
 */
class AssetTransferInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('asset_transfer.sections.transfer_details'))
                    ->schema([
                        TextEntry::make('asset.asset_tag')
                            ->label(__('asset_transfer.columns.asset_tag')),

                        TextEntry::make('asset.name')
                            ->label(__('asset_transfer.columns.asset_name')),

                        TextEntry::make('transfer_date')
                            ->label(__('asset_transfer.fields.transfer_date'))
                            ->date('d M Y'),

                        TextEntry::make('status')
                            ->label(__('asset_transfer.fields.status'))
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => __("asset_transfer.status.{$state}"))
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make(__('asset_transfer.sections.parties_involved'))
                    ->schema([
                        TextEntry::make('fromUser.name')
                            ->label(__('asset_transfer.fields.from_user_id'))
                            ->placeholder('-'),

                        TextEntry::make('toUser.name')
                            ->label(__('asset_transfer.fields.to_user_id')),

                        TextEntry::make('initiator.name')
                            ->label(__('asset_transfer.fields.initiated_by')),

                        TextEntry::make('approver.name')
                            ->label(__('asset_transfer.fields.approved_by'))
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make(__('asset_transfer.sections.location_notes'))
                    ->schema([
                        TextEntry::make('from_location')
                            ->label(__('asset_transfer.fields.from_location'))
                            ->placeholder('-'),

                        TextEntry::make('to_location')
                            ->label(__('asset_transfer.fields.to_location'))
                            ->placeholder('-'),

                        TextEntry::make('notes')
                            ->label(__('asset_transfer.fields.notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('cancellation_reason')
                            ->label(__('asset_transfer.fields.cancellation_reason'))
                            ->placeholder('-')
                            ->visible(fn ($record): bool => $record->status === 'cancelled')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
