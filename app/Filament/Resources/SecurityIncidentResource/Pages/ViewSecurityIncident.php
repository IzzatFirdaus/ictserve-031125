<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityIncidentResource\Pages;

use App\Filament\Resources\SecurityIncidentResource;
use App\Models\SecurityIncident;
use App\Services\SecurityIncidentService;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * View Security Incident Page
 *
 * PKS CSIRT Integration (Requirement 28) - Incident Detail View with Timeline
 *
 * @trace D03-FR-028 (CSIRT Integration)
 * @trace Requirements 28.1, 28.2, 28.3, 28.4, 28.5
 */
class ViewSecurityIncident extends ViewRecord
{
    protected static string $resource = SecurityIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Kemaskini'),

            Actions\Action::make('escalate_csirt')
                ->label('Eskalasi ke CSIRT')
                ->icon(Heroicon::OutlinedBellAlert)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Eskalasi ke CSIRT MOTAC')
                ->modalDescription('Adakah anda pasti untuk menghantar notifikasi eskalasi ke CSIRT MOTAC?')
                ->modalSubmitActionLabel('Ya, Eskalasi')
                ->visible(fn (SecurityIncident $record): bool => $record->csirt_notified_at === null && $record->requires_escalation)
                ->action(function (SecurityIncident $record): void {
                    $service = app(SecurityIncidentService::class);
                    $service->escalateToCSIRT($record);
                    Notification::make()->title('Berjaya')->body('Insiden telah dieskalasi ke CSIRT MOTAC.')->success()->send();
                }),

            Actions\Action::make('report_nacsa')
                ->label('Lapor ke NACSA')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Lapor ke NACSA')
                ->modalDescription('Adakah anda pasti untuk menghantar laporan ke NACSA?')
                ->modalSubmitActionLabel('Ya, Hantar')
                ->visible(fn (SecurityIncident $record): bool => $record->nacsa_reported_at === null)
                ->action(function (SecurityIncident $record): void {
                    $service = app(SecurityIncidentService::class);
                    $reportId = $service->submitToNACSA($record);
                    Notification::make()->title('Berjaya')->body("Laporan NACSA dihantar dengan ID: {$reportId}")->success()->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Insiden')
                ->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('incident_number')->label('No. Insiden')->weight('bold')->copyable(),
                        TextEntry::make('type')->label('Jenis')->badge()
                            ->formatStateUsing(fn (string $state): string => SecurityIncident::getTypes()[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                SecurityIncident::TYPE_DATA_BREACH, SecurityIncident::TYPE_DATA_EXFILTRATION => 'danger',
                                SecurityIncident::TYPE_UNAUTHORIZED_ACCESS, SecurityIncident::TYPE_BRUTE_FORCE => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('severity')->label('Keterukan')->badge()
                            ->formatStateUsing(fn (string $state): string => SecurityIncident::getSeverities()[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                SecurityIncident::SEVERITY_CRITICAL => 'danger',
                                SecurityIncident::SEVERITY_HIGH => 'warning',
                                SecurityIncident::SEVERITY_MEDIUM => 'primary',
                                default => 'gray',
                            }),
                        TextEntry::make('status')->label('Status')->badge()
                            ->formatStateUsing(fn (string $state): string => SecurityIncident::getStatuses()[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                SecurityIncident::STATUS_DETECTED, SecurityIncident::STATUS_ESCALATED => 'danger',
                                SecurityIncident::STATUS_INVESTIGATING => 'warning',
                                SecurityIncident::STATUS_RESOLVED, SecurityIncident::STATUS_CLOSED => 'success',
                                default => 'gray',
                            }),
                    ]),
                    TextEntry::make('title')->label('Tajuk')->columnSpanFull(),
                    TextEntry::make('description')->label('Penerangan')->columnSpanFull(),
                ]),

            Section::make('Maklumat Teknikal')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('source_ip')->label('IP Sumber')->placeholder('-')->copyable(),
                        TextEntry::make('target_system')->label('Sistem Sasaran')->placeholder('-'),
                        TextEntry::make('detected_at')->label('Tarikh Dikesan')->dateTime('d M Y, H:i:s'),
                    ]),
                    Grid::make(2)->schema([
                        TextEntry::make('detectedByUser.name')->label('Dikesan Oleh')->placeholder('Sistem'),
                        TextEntry::make('assignedToUser.name')->label('Ditugaskan Kepada')->placeholder('Belum ditugaskan'),
                    ]),
                ])
                ->collapsible(),

            Section::make('Status Pelaporan PKS')
                ->schema([
                    Grid::make(3)->schema([
                        IconEntry::make('csirt_status')->label('CSIRT MOTAC')->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckCircle)->falseIcon(Heroicon::OutlinedXCircle)
                            ->trueColor('success')->falseColor('gray')
                            ->getStateUsing(fn (SecurityIncident $record): bool => $record->csirt_notified_at !== null),
                        IconEntry::make('nacsa_status')->label('NACSA')->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckCircle)->falseIcon(Heroicon::OutlinedXCircle)
                            ->trueColor('success')->falseColor('gray')
                            ->getStateUsing(fn (SecurityIncident $record): bool => $record->nacsa_reported_at !== null),
                        IconEntry::make('mycert_status')->label('MyCERT')->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckCircle)->falseIcon(Heroicon::OutlinedXCircle)
                            ->trueColor('success')->falseColor('gray')
                            ->getStateUsing(fn (SecurityIncident $record): bool => $record->mycert_reported_at !== null),
                    ]),
                    Grid::make(3)->schema([
                        TextEntry::make('csirt_notified_at')->label('CSIRT Dimaklumkan')->dateTime('d M Y, H:i')->placeholder('-'),
                        TextEntry::make('nacsa_report_id')->label('ID Laporan NACSA')->placeholder('-')->copyable(),
                        TextEntry::make('mycert_report_id')->label('ID Laporan MyCERT')->placeholder('-')->copyable(),
                    ]),
                ])
                ->collapsible(),

            Section::make('Garis Masa Insiden')
                ->schema([
                    RepeatableEntry::make('timeline')->label('')->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('timestamp')->label('Masa')->dateTime('d M Y, H:i:s'),
                            TextEntry::make('action')->label('Tindakan')->weight('bold'),
                            TextEntry::make('details')->label('Butiran')->placeholder('-')->columnSpan(2),
                        ]),
                    ])->contained(false),
                ])
                ->collapsible(),

            Section::make('Penyelesaian')
                ->schema([
                    TextEntry::make('resolution_summary')->label('Ringkasan Penyelesaian')->placeholder('Belum diselesaikan')->columnSpanFull(),
                    TextEntry::make('lessons_learned')->label('Pengajaran')->placeholder('-')->columnSpanFull(),
                    Grid::make(3)->schema([
                        TextEntry::make('contained_at')->label('Dibendung')->dateTime('d M Y, H:i')->placeholder('-'),
                        TextEntry::make('resolved_at')->label('Diselesaikan')->dateTime('d M Y, H:i')->placeholder('-'),
                        TextEntry::make('closed_at')->label('Ditutup')->dateTime('d M Y, H:i')->placeholder('-'),
                    ]),
                ])
                ->visible(fn (SecurityIncident $record): bool => $record->resolution_summary !== null || $record->resolved_at !== null)
                ->collapsible(),
        ]);
    }
}
