<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

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
