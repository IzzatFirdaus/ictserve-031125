<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * User Form Schema
 *
 * Filament form schema for User model with role-based field visibility.
 * Only superuser can change user roles.
 *
 * @trace D03-FR-003.3 (User management in Filament)
 * @trace D04 §6.3 (Filament User Management)
 * @trace D10 §7 (Component Documentation Standards)
 * @trace D12 §9 (Filament Form Standards)
 * @trace D14 §8 (MOTAC UI Standards)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Maklumat Asas')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('users.full_name')),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label(__('users.email_address')),

                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->label(__('users.password'))
                            ->helperText('Biarkan kosong untuk kekalkan kata laluan semasa (mod kemaskini)'),

                        Select::make('role')
                            ->options([
                                'staff' => 'Staf',
                                'approver' => 'Pelulus (Gred 41+)',
                                'admin' => 'Admin',
                                'superuser' => 'Superuser',
                            ])
                            ->default('staff')
                            ->required()
                            ->label(__('users.role'))
                            ->disabled(function (): bool {
                                /** @var \App\Models\User|null $user */
                                $user = Auth::user();

                                return ! ($user?->hasRole('superuser') ?? false);
                            })
                            ->helperText('Hanya superuser boleh menukar peranan'),

                        Toggle::make('is_active')
                            ->default(true)
                            ->label(__('users.active_status')),
                    ])
                    ->columns(2),

                Section::make('Maklumat Organisasi')
                    ->schema([
                        TextInput::make('staff_id')
                            ->maxLength(255)
                            ->label(__('users.staff_id')),

                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->searchable()
                            ->preload()
                            ->label(__('users.division')),

                        Select::make('grade_id')
                            ->relationship('grade', 'name')
                            ->searchable()
                            ->preload()
                            ->label(__('users.grade')),

                        Select::make('position_id')
                            ->relationship('position', 'name')
                            ->searchable()
                            ->preload()
                            ->label(__('users.position')),
                    ])
                    ->columns(2),

                Section::make('Maklumat Perhubungan')
                    ->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->label(__('users.office_phone')),

                        TextInput::make('mobile')
                            ->tel()
                            ->maxLength(255)
                            ->label(__('users.mobile_phone')),
                    ])
                    ->columns(2),
            ]);
    }
}
