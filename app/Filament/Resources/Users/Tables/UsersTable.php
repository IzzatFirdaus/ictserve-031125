<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                    ->label(__('widgets.name')),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label(__('widgets.email')),

                TextColumn::make('role')
                    ->badge()
                    ->colors([
                        'primary' => 'staff',
                        'warning' => 'approver',
                        'success' => 'admin',
                        'danger' => 'superuser',
                    ])
                    ->sortable()
                    ->label(__('widgets.role')),

                TextColumn::make('staff_id')
                    ->searchable()
                    ->label(__('widgets.staff_id'))
                    ->toggleable(),

                TextColumn::make('division.name_ms')
                    ->searchable()
                    ->sortable()
                    ->label(__('widgets.division'))
                    ->toggleable(),

                TextColumn::make('grade.name_ms')
                    ->searchable()
                    ->sortable()
                    ->label(__('widgets.grade'))
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label(__('widgets.active')),

                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('widgets.last_login'))
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('widgets.created'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('widgets.updated'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'staff' => __('users.role_staff'),
                        'approver' => __('users.role_approver'),
                        'admin' => __('users.role_admin'),
                        'superuser' => __('users.role_superuser'),
                    ])
                    ->label(__('users.role')),

                SelectFilter::make('division_id')
                    ->relationship('division', 'name_ms')
                    ->label(__('users.division'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('grade_id')
                    ->relationship('grade', 'name_ms')
                    ->label(__('users.grade'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => __('users.status_active'),
                        '0' => __('users.status_inactive'),
                    ])
                    ->label(__('users.status')),

                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('impersonate')
                    ->label('Lakon Sebagai')
                    ->icon('heroicon-o-user-plus')
                    ->url(fn (User $record) => route('impersonate.start', $record))
                    ->openUrlInNewTab(false)
                    ->visible(function (User $record): bool {
                        /** @var User|null $user */
                        $user = Auth::user();

                        return $user?->hasRole('Super Admin') && $record->id !== Auth::id();
                    }),
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
