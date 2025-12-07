<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Events\ApiTokenCreated;
use App\Filament\Resources\ApiTokenResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Create API Token Page
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.4
 * @version 3.5.0
 */
class CreateApiToken extends CreateRecord
{
    protected static string $resource = ApiTokenResource::class;

    public function getTitle(): string
    {
        return __('admin.create_token');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure token is created for current user
        $data['tokenable_type'] = 'App\\Models\\User';
        $data['tokenable_id'] = Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = Auth::user();
        $name = $this->record->name;
        $abilities = $this->record->abilities ?? ['*'];

        // Create the actual token (Sanctum)
        $token = $user->createToken($name, $abilities, $this->record->expires_at);

        // Dispatch broadcast event for real-time UI update (Echo/Reverb)
        // Frontend listeners in resources/js/portal-echo.js will receive this
        ApiTokenCreated::dispatch($user, $token->accessToken);

        // Store plain token for one-time display
        session(['new_api_token' => $token->plainTextToken]);

        Notification::make()
            ->success()
            ->title(__('admin.token_created'))
            ->body(__('admin.token_created_copy_now'))
            ->persistent()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
