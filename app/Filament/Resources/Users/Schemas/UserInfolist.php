<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

/**
 * Component name: User Infolist Schema
 * Description: Filament infolist schema defining read-only view layout for user details
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.1 (User Management)
 * @trace D04 §3.1 (Admin Panel)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */
class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('role')
                    ->badge(),
                TextEntry::make('staff_id')
                    ->placeholder('-'),
                TextEntry::make('division.id')
                    ->label('Division')
                    ->placeholder('-'),
                TextEntry::make('grade.id')
                    ->label('Grade')
                    ->placeholder('-'),
                TextEntry::make('position.id')
                    ->label('Position')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('mobile')
                    ->placeholder('-'),
                TextEntry::make('bio')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('avatar')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('notification_preferences')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('last_login_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (User $record): bool => $record->trashed()),
            ]);
    }
}
