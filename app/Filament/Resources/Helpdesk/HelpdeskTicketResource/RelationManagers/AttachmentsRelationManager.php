<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\HelpdeskTicketResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Attachments Relation Manager for Helpdesk Tickets
 *
 * Manages file attachments with proper validation and RBAC.
 * Supports multiple file types with size limits per requirements.
 *
 * @trace Requirements: Requirement 1.4, Requirement 5.2, Requirement 6.2
 */
class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Lampiran';

    protected static ?string $recordTitleAttribute = 'original_filename';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label('Fail')
                    ->required()
                    ->disk('private')
                    ->directory('helpdesk-attachments')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(5120) // 5MB
                    ->downloadable()
                    ->previewable()
                    ->helperText('Jenis fail yang diterima: JPG, PNG, PDF, DOC, DOCX. Saiz maksimum: 5MB')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_filename')
                    ->label('Nama Fail')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-paper-clip'),

                TextColumn::make('mime_type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'image') => 'success',
                        str_contains($state, 'pdf') => 'danger',
                        str_contains($state, 'word') => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('file_size')
                    ->label('Saiz')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2).' KB')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Dimuat Naik Oleh')
                    ->placeholder('Tetamu')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dimuat Naik')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        $data['disk'] = 'private';

                        // Extract file information
                        if (isset($data['file_path'])) {
                            $filePath = $data['file_path'];
                            $data['filename'] = basename($filePath);
                            $data['original_filename'] = $data['filename'];
                            $data['mime_type'] = Storage::disk('private')->getMimeType($filePath);
                            $data['file_size'] = Storage::disk('private')->size($filePath);
                        }

                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Muat Turun')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn ($record) => Storage::disk($record->disk)->download($record->file_path, $record->original_filename)),

                DeleteAction::make()
                    ->visible(fn ($record) => $record->user_id === Auth::id() || Auth::user()?->hasAdminAccess())
                    ->before(fn ($record) => Storage::disk($record->disk)->delete($record->file_path)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->hasAdminAccess())
                        ->before(fn ($records) => $records->each(fn ($record) => Storage::disk($record->disk)->delete($record->file_path))),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
