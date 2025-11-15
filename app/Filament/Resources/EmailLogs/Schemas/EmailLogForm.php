<?php

namespace App\Filament\Resources\EmailLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EmailLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('recipient_email')
                    ->email()
                    ->required(),
                TextInput::make('recipient_name')
                    ->default(null),
                TextInput::make('subject')
                    ->required(),
                TextInput::make('mailable_class')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('queued'),
                TextInput::make('message_id')
                    ->default(null),
                Textarea::make('status_message')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('queued_at')
                    ->required(),
                DateTimePicker::make('sent_at'),
                DateTimePicker::make('failed_at'),
            ]);
    }
}
