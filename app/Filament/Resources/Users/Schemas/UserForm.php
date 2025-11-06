<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Full Name'),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('Email Address'),

                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->label('Password')
                            ->helperText('Leave blank to keep current password (edit mode)'),

                        Select::make('role')
                            ->options([
                                'staff' => 'Staff',
                                'approver' => 'Approver (Grade 41+)',
                                'admin' => 'Admin',
                                'superuser' => 'Superuser',
                            ])
                            ->default('staff')
                            ->required()
                            ->label('Role')
                            ->disabled(fn () => ! auth()->user()->isSuperuser())
                            ->helperText('Only superuser can change roles'),

                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Active Status'),
                    ])
                    ->columns(2),

                Section::make('Organizational Information')
                    ->schema([
                        TextInput::make('staff_id')
                            ->maxLength(255)
                            ->label('Staff ID'),

                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Division'),

                        Select::make('grade_id')
                            ->relationship('grade', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Grade'),

                        Select::make('position_id')
                            ->relationship('position', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Position'),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->label('Office Phone'),

                        TextInput::make('mobile')
                            ->tel()
                            ->maxLength(255)
                            ->label('Mobile Phone'),
                    ])
                    ->columns(2),
            ]);
    }
}
