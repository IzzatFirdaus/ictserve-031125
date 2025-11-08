<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

/**
 * Users Table Configuration
 *
 * Filament table configuration for User model listing.
 *
 * @trace D03-FR-003.3 (User management in Filament)
 * @trace D04 §6.3 (Filament User Management)
 * @trace D10 §7 (Component Documentation Standards)
 * @trace D12 §9 (Filament Table Standards)
 * @trace D14 §8 (MOTAC UI Standards)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),

                TextColumn::make('role')
                    ->badge()
                    ->colors([
                        'primary' => 'staff',
                        'warning' => 'approver',
                        'success' => 'admin',
                        'danger' => 'superuser',
                    ])
                    ->sortable()
                    ->label('Role'),

                TextColumn::make('staff_id')
                    ->searchable()
                    ->label('Staff ID')
                    ->toggleable(),

                TextColumn::make('division.name')
                    ->searchable()
                    ->sortable()
                    ->label('Division')
                    ->toggleable(),

                TextColumn::make('grade.name')
                    ->searchable()
                    ->sortable()
                    ->label('Grade')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label('Active'),

                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Login')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Updated')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'staff' => 'Staff',
                        'approver' => 'Approver',
                        'admin' => 'Admin',
                        'superuser' => 'Superuser',
                    ])
                    ->label('Role'),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Status'),

                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
