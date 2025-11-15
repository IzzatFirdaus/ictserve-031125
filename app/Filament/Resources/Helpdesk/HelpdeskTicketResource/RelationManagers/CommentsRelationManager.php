<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\HelpdeskTicketResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Comments Relation Manager for Helpdesk Tickets
 *
 * Manages internal and external comments with proper RBAC.
 * Internal comments are only visible to admin/superuser roles.
 *
 * @trace Requirements: Requirement 2.5, Requirement 3.3
 */
class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('helpdesk.comments');
    }

    protected static ?string $recordTitleAttribute = 'comment';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('commenter_name')
                    ->label(__('helpdesk.commenter_name'))
                    ->maxLength(255)
                    ->default(fn () => Auth::user()?->name)
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('commenter_email')
                    ->label(__('helpdesk.commenter_email'))
                    ->email()
                    ->maxLength(255)
                    ->default(fn () => Auth::user()?->email)
                    ->disabled()
                    ->dehydrated(false),

                Textarea::make('comment')
                    ->label(__('helpdesk.comments'))
                    ->required()
                    ->rows(4)
                    ->maxLength(5000)
                    ->columnSpanFull(),

                Checkbox::make('is_internal')
                    ->label(__('helpdesk.internal_comment'))
                    ->helperText(__('helpdesk.internal_comment_help') ?? 'Internal comments are only visible to administrators')
                    ->default(false)
                    ->visible(fn () => Auth::user()?->hasAdminAccess()),

                Checkbox::make('is_resolution')
                    ->label(__('helpdesk.resolution_comment'))
                    ->helperText('Tandakan jika ini adalah komen penyelesaian akhir')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('helpdesk.commenter_name'))
                    ->placeholder('Tetamu')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label(__('helpdesk.comments'))
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                IconColumn::make('is_internal')
                    ->label(__('helpdesk.comment_type_internal'))
                    ->boolean()
                    ->alignCenter()
                    ->tooltip(fn ($record) => $record->is_internal ? 'Komen dalaman' : 'Komen awam'),

                IconColumn::make('is_resolution')
                    ->label(__('helpdesk.resolution_comment'))
                    ->boolean()
                    ->alignCenter()
                    ->tooltip(fn ($record) => $record->is_resolution ? 'Komen penyelesaian' : 'Komen biasa'),

                TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_internal')
                    ->label('Jenis Komen')
                    ->options([
                        '1' => 'Dalaman',
                        '0' => 'Awam',
                    ])
                    ->visible(fn () => Auth::user()?->hasAdminAccess()),

                SelectFilter::make('is_resolution')
                    ->label('Penyelesaian')
                    ->options([
                        '1' => 'Ya',
                        '0' => 'Tidak',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        $data['commenter_name'] = Auth::user()?->name;
                        $data['commenter_email'] = Auth::user()?->email;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => $record->user_id === Auth::id() || Auth::user()?->hasAdminAccess()),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->user_id === Auth::id() || Auth::user()?->hasAdminAccess()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->hasAdminAccess()),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                // Hide internal comments from non-admin users
                if (! Auth::user()?->hasAdminAccess()) {
                    $query->where('is_internal', false);
                }

                return $query;
            });
    }
}
