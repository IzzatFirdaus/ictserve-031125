<?php

declare(strict_types=1);

namespace App\Filament\Resources\FailedJobs;

use App\Filament\Resources\FailedJobs\Pages\CreateFailedJob;
use App\Filament\Resources\FailedJobs\Pages\EditFailedJob;
use App\Filament\Resources\FailedJobs\Pages\ListFailedJobs;
use App\Filament\Resources\FailedJobs\Schemas\FailedJobForm;
use App\Filament\Resources\FailedJobs\Tables\FailedJobsTable;
use App\Models\FailedJob;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Sumber Filament untuk Tugas Gagal
 *
 * Menyediakan antara muka untuk pengurusan tugas gagal dengan ciri:
 * - Paparan log terperinci
 * - Tindakan cuba semula
 * - Pematuhan WCAG 2.2 AA
 *
 * Selaras dengan D15 v3.6.1: Bahasa Melayu sahaja
 *
 * @trace Requirements 9.1, 9.2
 */
class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    public static function getNavigationLabel(): string
    {
        return 'Tugas Gagal';
    }

    public static function getModelLabel(): string
    {
        return 'Tugas Gagal';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tugas Gagal';
    }

    public static function form(Schema $schema): Schema
    {
        return FailedJobForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FailedJobsTable::configure($table);
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
            'index' => ListFailedJobs::route('/'),
            'create' => CreateFailedJob::route('/create'),
            'edit' => EditFailedJob::route('/{record}/edit'),
        ];
    }

    /**
     * Check if the current user can view any failed jobs.
     * Only superuser role has access.
     */
    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasRole('superuser');
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasRole('superuser');
    }

    /**
     * @return Builder<FailedJob>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->latest('failed_at');
    }
}
