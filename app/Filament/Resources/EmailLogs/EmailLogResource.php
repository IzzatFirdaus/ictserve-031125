<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailLogs;

use App\Filament\Clusters\System;
use App\Filament\Resources\EmailLogs\Pages\CreateEmailLog;
use App\Filament\Resources\EmailLogs\Pages\EditEmailLog;
use App\Filament\Resources\EmailLogs\Pages\ListEmailLogs;
use App\Filament\Resources\EmailLogs\Schemas\EmailLogForm;
use App\Filament\Resources\EmailLogs\Tables\EmailLogsTable;
use App\Models\EmailLog;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Sumber Filament untuk Log E-mel
 *
 * Menyediakan antara muka baca sahaja untuk log e-mel dengan ciri:
 * - Paparan log terperinci
 * - Penapisan mengikut status dan tarikh
 * - Pematuhan WCAG 2.2 AA
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 9.1, 9.2
 */
class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $cluster = System::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.system.email_logs');
    }

    public static function getModelLabel(): string
    {
        return __('filament.system.email_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.system.email_logs');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Check if the current user can view any email logs.
     * Only admin and superuser roles have access.
     */
    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasAnyRole(['admin', 'superuser']);
    }

    public static function form(Schema $schema): Schema
    {
        return EmailLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailLogs::route('/'),
            'create' => CreateEmailLog::route('/create'),
            'edit' => EditEmailLog::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<EmailLog>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->latest();
    }
}
