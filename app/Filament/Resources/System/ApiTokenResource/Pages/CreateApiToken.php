<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\ApiTokenResource\Pages;

use App\Filament\Resources\System\ApiTokenResource;
use App\Models\User;
use App\Services\ApiTokenService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Create API Token Page
 *
 * Handles API token creation with abilities and expiration configuration.
 * Shows the plain text token only once after creation.
 *
 * @trace Requirements 37.1, 37.2
 */
class CreateApiToken extends CreateRecord
{
    protected static string $resource = ApiTokenResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Create API Token');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // We'll handle creation manually in handleRecordCreation
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var User|null $user */
        $user = User::find($data['user_id'] ?? $data['tokenable_id'] ?? null);

        if ($user === null) {
            Notification::make()
                ->danger()
                ->title(__('Error'))
                ->body(__('User not found.'))
                ->send();

            throw new \RuntimeException('User not found');
        }

        $service = app(ApiTokenService::class);

        $abilities = $data['abilities'] ?? ['*'];
        $expirationDays = (int) ($data['expiration_days'] ?? 30);

        // Convert 0 to null for never expires
        $expirationDays = $expirationDays === 0 ? null : $expirationDays;

        $token = $service->createToken(
            user: $user,
            name: $data['name'],
            abilities: $abilities,
            expirationDays: $expirationDays
        );

        // Show the plain text token to the user (only time it's visible)
        Notification::make()
            ->success()
            ->title(__('Token Created Successfully'))
            ->body(__('Copy this token now. It will not be shown again: ').$token->plainTextToken)
            ->persistent()
            ->send();

        return $token->accessToken;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null; // We handle notification in handleRecordCreation
    }
}
