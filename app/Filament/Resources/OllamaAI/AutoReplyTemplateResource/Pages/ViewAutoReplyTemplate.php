<?php

declare(strict_types=1);

namespace App\Filament\Resources\OllamaAI\AutoReplyTemplateResource\Pages;

use App\Filament\Resources\OllamaAI\AutoReplyTemplateResource;
use App\Models\AutoReplyTemplate;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman Lihat Template Auto-Reply
 *
 * @trace Requirements 3.4, 5.1
 */
class ViewAutoReplyTemplate extends ViewRecord
{
    protected static string $resource = AutoReplyTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Action::make('activate')
                ->label(__('ollama.template.activate'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var AutoReplyTemplate $record */
                    $record = $this->record;
                    $record->activate();

                    Notification::make()
                        ->title('Template diaktifkan')
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => $this->record instanceof AutoReplyTemplate && ! $this->record->isActive()),

            Action::make('duplicate')
                ->label(__('ollama.template.duplicate'))
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->action(function (): void {
                    $userId = Auth::id();

                    if (! is_int($userId)) {
                        Notification::make()
                            ->danger()
                            ->title(__('ollama.common.session_expired_title'))
                            ->body(__('ollama.common.session_expired_body'))
                            ->send();

                        return;
                    }

                    /** @var AutoReplyTemplate $record */
                    $record = $this->record;
                    $newTemplate = $record->replicate();
                    $newTemplate->name = "{$record->name} (Salinan)";
                    $newTemplate->status = AutoReplyTemplate::STATUS_DRAFT;
                    $newTemplate->created_by = $userId;
                    $newTemplate->save();

                    Notification::make()
                        ->title(__('ollama.template.duplicated_success'))
                        ->success()
                        ->send();

                    $this->redirect(AutoReplyTemplateResource::getUrl('edit', ['record' => $newTemplate]));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('ollama.template.view_title');
    }
}
