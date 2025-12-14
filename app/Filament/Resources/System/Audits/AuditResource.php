<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\Audits;

use App\Filament\Clusters\System;
use App\Filament\Resources\System\Audits\Pages\ListAudits;
use App\Filament\Resources\System\Audits\Pages\ViewAudit;
use App\Filament\Resources\System\Audits\Schemas\AuditInfolist;
use App\Filament\Resources\System\Audits\Tables\AuditsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit;

/**
 * Audit Resource
 *
 * Filament resource for viewing audit trail records.
 * Superuser-only access with 7-year retention (PDPA 2010).
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-010 (Audit Trail), D09 §9 (Audit Requirements)
 * Traceability: Phase 9.1 - Audit Resource Implementation
 * WCAG 2.2 AA: Full keyboard navigation, ARIA labels, 4.5:1 contrast
 */
class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $cluster = System::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    /**
     * Get the navigation label for the resource.
     */
    public static function getNavigationLabel(): string
    {
        return 'Jejak Audit';
    }

    /**
     * Get the plural label for the resource.
     */
    public static function getPluralLabel(): string
    {
        return 'Jejak Audit';
    }

    /**
     * Get the model label for the resource.
     */
    public static function getModelLabel(): string
    {
        return 'Rekod Audit';
    }

    /**
     * Determine if the resource should be registered in navigation.
     * Superuser-only access.
     */
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('superuser') ?? false;
    }

    /**
     * Determine if the resource can be accessed.
     * Superuser-only access.
     */
    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('superuser') ?? false;
    }

    /**
     * Configure the infolist schema.
     */
    public static function infolist(Schema $schema): Schema
    {
        return AuditInfolist::configure($schema);
    }

    /**
     * Configure the table.
     */
    public static function table(Table $table): Table
    {
        return AuditsTable::configure($table);
    }

    /**
     * Get the pages for the resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
            'view' => ViewAudit::route('/{record}'),
        ];
    }
}
