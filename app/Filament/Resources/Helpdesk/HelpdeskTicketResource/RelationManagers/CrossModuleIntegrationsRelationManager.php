<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\HelpdeskTicketResource\RelationManagers;

use App\Models\CrossModuleIntegration;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Cross-Module Integrations Relation Manager
 *
 * Manages integration records between helpdesk tickets and asset loan system.
 * Displays asset linkage and cross-module events with proper RBAC.
 *
 * @trace Requirements: Requirement 2.2, Requirement 2.3, Requirement 2.5
 */
class CrossModuleIntegrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'crossModuleIntegrations';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('helpdesk.cross_module_integrations');
    }

    protected static ?string $recordTitleAttribute = 'integration_type';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('integration_type')
                    ->label(__('helpdesk.integration_type'))
                    ->options([
                        CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT => 'Laporan Kerosakan Aset',
                        CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST => 'Permintaan Penyelenggaraan',
                        CrossModuleIntegration::TYPE_ASSET_TICKET_LINK => 'Pautan Aset-Tiket',
                    ])
                    ->required()
                    ->disabled(),

                Select::make('trigger_event')
                    ->label(__('helpdesk.integration_trigger_event'))
                    ->options([
                        CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED => 'Aset Dipulangkan Rosak',
                        CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED => 'Aset Dipilih dalam Tiket',
                        CrossModuleIntegration::EVENT_MAINTENANCE_SCHEDULED => 'Penyelenggaraan Dijadualkan',
                    ])
                    ->required()
                    ->disabled(),

                Select::make('loan_application_id')
                    ->relationship('assetLoan', 'id')
                    ->label(__('helpdesk.loan_application'))
                    ->disabled()
                    ->helperText('Pautan kepada permohonan pinjaman aset berkaitan'),

                KeyValue::make('integration_data')
                    ->label(__('helpdesk.integration_data'))
                    ->disabled()
                    ->columnSpanFull(),

                DateTimePicker::make('processed_at')
                    ->label(__('helpdesk.process_date'))
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('integration_type')
                    ->label(__('helpdesk.integration_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT => 'danger',
                        CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST => 'warning',
                        CrossModuleIntegration::TYPE_ASSET_TICKET_LINK => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT => 'Laporan Kerosakan',
                        CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST => 'Penyelenggaraan',
                        CrossModuleIntegration::TYPE_ASSET_TICKET_LINK => 'Pautan Aset',
                        default => $state,
                    }),

                TextColumn::make('trigger_event')
                    ->label(__('helpdesk.integration_trigger_event'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED => 'Aset Rosak',
                        CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED => 'Aset Dipilih',
                        CrossModuleIntegration::EVENT_MAINTENANCE_SCHEDULED => 'Penyelenggaraan',
                        default => $state,
                    })
                    ->wrap(),

                TextColumn::make('assetLoan.id')
                    ->label(__('helpdesk.loan_id'))
                    ->placeholder('-')
                    ->url(fn ($record) => $record->assetLoan
                        ? route('filament.admin.resources.loan-applications.view', $record->assetLoan)
                        : null)
                    ->color('info'),

                IconColumn::make('processed')
                    ->label(__('helpdesk.processed'))
                    ->state(fn ($record) => $record->isProcessed())
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('processed_at')
                    ->label(__('helpdesk.process_date'))
                    ->dateTime('d M Y h:i A')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('helpdesk.created_at'))
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('integration_type')
                    ->label(__('helpdesk.integration_type'))
                    ->options([
                        CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT => 'Laporan Kerosakan',
                        CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST => 'Penyelenggaraan',
                        CrossModuleIntegration::TYPE_ASSET_TICKET_LINK => 'Pautan Aset',
                    ]),

                SelectFilter::make('processed')
                    ->label(__('helpdesk.processed'))
                    ->options([
                        '1' => 'Diproses',
                        '0' => 'Belum Diproses',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === '1') {
                            return $query->processed();
                        }
                        if ($data['value'] === '0') {
                            return $query->unprocessed();
                        }

                        return $query;
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => Auth::user()?->hasAdminAccess())
                    ->disabled()
                    ->tooltip(__('helpdesk.integration_created_automatically')),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markProcessed')
                    ->label(__('helpdesk.mark_as_processed'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->isProcessed() && Auth::user()?->hasAdminAccess())
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->markAsProcessed()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->isSuperuser()),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('helpdesk.no_cross_module_integrations'))
            ->emptyStateDescription(__('helpdesk.no_cross_module_integrations_description'));
    }
}
