<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityIncidentResource\Pages;
use App\Models\SecurityIncident;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Filament Resource: Security Incident Management Dashboard
 *
 * PKS CSIRT Integration (Requirement 28) - Incident Management Dashboard
 *
 * Provides comprehensive admin interface for:
 * - Viewing and managing security incidents
 * - Incident timeline visualization
 * - CSIRT escalation tracking
 * - NACSA/MyCERT reporting status
 * - 7-year incident log retention compliance
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-028 (CSIRT Integration)
 * @trace Requirements 28.1, 28.2, 28.3, 28.4, 28.5
 *
 * @version 1.0.0
 *
 * @created 2025-12-25
 */
class SecurityIncidentResource extends Resource
{
    protected static ?string $model = SecurityIncident::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static string|UnitEnum|null $navigationGroup = 'Keselamatan';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Insiden Keselamatan';
    }

    public static function getModelLabel(): string
    {
        return 'Insiden Keselamatan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Insiden Keselamatan';
    }

    public static function getNavigationBadge(): ?string
    {
        $openCount = SecurityIncident::query()->open()->count();

        return $openCount > 0 ? (string) $openCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $criticalCount = SecurityIncident::query()
            ->open()
            ->where('severity', SecurityIncident::SEVERITY_CRITICAL)
            ->count();

        if ($criticalCount > 0) {
            return 'danger';
        }

        $highCount = SecurityIncident::query()
            ->open()
            ->where('severity', SecurityIncident::SEVERITY_HIGH)
            ->count();

        return $highCount > 0 ? 'warning' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Maklumat Insiden')
                    ->schema([
                        Forms\Components\TextInput::make('incident_number')
                            ->label('No. Insiden')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('type')
                            ->label('Jenis Insiden')
                            ->options(SecurityIncident::getTypes())
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('severity')
                            ->label('Tahap Keterukan')
                            ->options(SecurityIncident::getSeverities())
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(SecurityIncident::getStatuses())
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('title')
                            ->label('Tajuk')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Penerangan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Maklumat Teknikal')
                    ->schema([
                        Forms\Components\TextInput::make('source_ip')
                            ->label('IP Sumber')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('target_system')
                            ->label('Sistem Sasaran')
                            ->columnSpan(1),

                        Forms\Components\Select::make('assigned_to_user_id')
                            ->label('Ditugaskan Kepada')
                            ->relationship('assignedToUser', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('detected_at')
                            ->label('Tarikh Dikesan')
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Status Pelaporan')
                    ->schema([
                        Forms\Components\Toggle::make('requires_escalation')
                            ->label('Memerlukan Eskalasi CSIRT')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('csirt_notified_at')
                            ->label('CSIRT Dimaklumkan')
                            ->disabled(),

                        Forms\Components\TextInput::make('nacsa_report_id')
                            ->label('ID Laporan NACSA')
                            ->disabled(),

                        Forms\Components\TextInput::make('mycert_report_id')
                            ->label('ID Laporan MyCERT')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Penyelesaian')
                    ->schema([
                        Forms\Components\Textarea::make('resolution_summary')
                            ->label('Ringkasan Penyelesaian')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('lessons_learned')
                            ->label('Pengajaran')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_false_positive')
                            ->label('Positif Palsu'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('incident_number')
                    ->label('No. Insiden')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => SecurityIncident::getTypes()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        SecurityIncident::TYPE_DATA_BREACH, SecurityIncident::TYPE_DATA_EXFILTRATION => 'danger',
                        SecurityIncident::TYPE_UNAUTHORIZED_ACCESS, SecurityIncident::TYPE_BRUTE_FORCE => 'warning',
                        SecurityIncident::TYPE_MALWARE, SecurityIncident::TYPE_PHISHING => 'danger',
                        SecurityIncident::TYPE_PRIVILEGE_ESCALATION => 'warning',
                        SecurityIncident::TYPE_DOS_ATTACK => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('severity')
                    ->label('Keterukan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => SecurityIncident::getSeverities()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        SecurityIncident::SEVERITY_CRITICAL => 'danger',
                        SecurityIncident::SEVERITY_HIGH => 'warning',
                        SecurityIncident::SEVERITY_MEDIUM => 'primary',
                        SecurityIncident::SEVERITY_LOW => 'success',
                        SecurityIncident::SEVERITY_INFO => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => SecurityIncident::getStatuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        SecurityIncident::STATUS_DETECTED => 'danger',
                        SecurityIncident::STATUS_INVESTIGATING => 'warning',
                        SecurityIncident::STATUS_ESCALATED => 'danger',
                        SecurityIncident::STATUS_CONTAINED => 'primary',
                        SecurityIncident::STATUS_ERADICATING => 'primary',
                        SecurityIncident::STATUS_RECOVERING => 'info',
                        SecurityIncident::STATUS_RESOLVED => 'success',
                        SecurityIncident::STATUS_CLOSED => 'gray',
                        SecurityIncident::STATUS_FALSE_POSITIVE => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Tajuk')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (SecurityIncident $record): string => $record->title),

                Tables\Columns\TextColumn::make('assignedToUser.name')
                    ->label('Ditugaskan')
                    ->placeholder('Belum ditugaskan')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('csirt_notified_at')
                    ->label('CSIRT')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn (SecurityIncident $record): bool => $record->csirt_notified_at !== null),

                Tables\Columns\TextColumn::make('detected_at')
                    ->label('Dikesan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Diselesaikan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Insiden')
                    ->options(SecurityIncident::getTypes())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('severity')
                    ->label('Tahap Keterukan')
                    ->options(SecurityIncident::getSeverities())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(SecurityIncident::getStatuses())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('assigned_to_user_id')
                    ->label('Ditugaskan Kepada')
                    ->relationship('assignedToUser', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('requires_escalation')
                    ->label('Memerlukan Eskalasi'),

                Tables\Filters\TernaryFilter::make('csirt_notified')
                    ->label('CSIRT Dimaklumkan')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('csirt_notified_at'),
                        false: fn (Builder $query) => $query->whereNull('csirt_notified_at'),
                    ),

                Tables\Filters\Filter::make('detected_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tarikh'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hingga Tarikh'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('detected_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('detected_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\ExportBulkAction::make()
                        ->label('Eksport Dipilih'),
                ]),
            ])
            ->defaultSort('detected_at', 'desc');
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
            'index' => Pages\ListSecurityIncidents::route('/'),
            'view' => Pages\ViewSecurityIncident::route('/{record}'),
            'edit' => Pages\EditSecurityIncident::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['detectedByUser', 'assignedToUser']);
    }

    public static function canCreate(): bool
    {
        return false; // Incidents are created automatically by detection system
    }

    public static function canDelete($record): bool
    {
        return false; // 7-year retention requirement per PKS 28.3
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user !== null && ($user->hasRole('admin') || $user->hasRole('superuser'));
    }
}
