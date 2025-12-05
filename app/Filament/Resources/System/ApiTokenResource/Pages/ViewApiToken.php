<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\ApiTokenResource\Pages;

use App\Filament\Resources\System\ApiTokenResource;
use App\Models\ApiTokenUsageLog;
use App\Models\User;
use App\Services\ApiTokenService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * View API Token Page
 *
 * Displays detailed information about an API token including usage statistics.
 *
 * @trace Requirements 37.1, 37.2, 37.3, 37.5
 */
class ViewApiToken extends ViewRecord
{
    protected static string $resource = ApiTokenResource::class;

    public function getTitle(): string|Htmlable
    {
        /** @var PersonalAccessToken $record */
        $record = $this->record;

        return __('API Token: :name', ['name' => $record->name]);
    }

    public function form(Schema $schema): Schema
    {
        /** @var PersonalAccessToken $record */
        $record = $this->record;

        return $schema
            ->components([
                Section::make(__('Token Information'))
                    ->components([
                        Placeholder::make('name')
                            ->label(__('Token Name'))
                            ->content(fn () => $record->name ?? '-'),

                        Placeholder::make('owner')
                            ->label(__('Owner'))
                            ->content(fn () => $record->tokenable?->name ?? '-'),

                        Placeholder::make('abilities')
                            ->label(__('Abilities'))
                            ->content(function () use ($record): string {
                                $abilities = $record->abilities ?? [];
                                if (\is_array($abilities)) {
                                    return \implode(', ', $abilities);
                                }

                                return (string) $abilities;
                            }),

                        Placeholder::make('created_at')
                            ->label(__('Created'))
                            ->content(fn () => $record->created_at?->format('d/m/Y H:i:s') ?? '-'),

                        Placeholder::make('expires_at')
                            ->label(__('Expires'))
                            ->content(function () use ($record): string {
                                if ($record->expires_at === null) {
                                    return __('Never');
                                }

                                $expiresAt = Carbon::parse($record->expires_at);
                                if ($expiresAt->isPast()) {
                                    return __('Expired on :date', ['date' => $expiresAt->format('d/m/Y H:i')]);
                                }

                                $daysRemaining = (int) Carbon::now()->diffInDays($expiresAt);

                                return $expiresAt->format('d/m/Y H:i').' ('.__(':days days remaining', ['days' => $daysRemaining]).')';
                            }),

                        Placeholder::make('last_used_at')
                            ->label(__('Last Used'))
                            ->content(fn () => $record->last_used_at?->format('d/m/Y H:i:s') ?? __('Never used')),
                    ])
                    ->columns(2),

                Section::make(__('Usage Statistics (Last 30 Days)'))
                    ->components([
                        Placeholder::make('usage_total')
                            ->label(__('Total Requests'))
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->count();
                            }),

                        Placeholder::make('usage_successful')
                            ->label(__('Successful Requests'))
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->where('response_status', '<', 400)
                                    ->count();
                            }),

                        Placeholder::make('usage_failed')
                            ->label(__('Failed Requests'))
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->where('response_status', '>=', 400)
                                    ->count();
                            }),

                        Placeholder::make('usage_endpoints')
                            ->label(__('Unique Endpoints'))
                            ->content(function () use ($record): int {
                                return ApiTokenUsageLog::where('personal_access_token_id', $record->id)
                                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                                    ->distinct('endpoint')
                                    ->count('endpoint');
                            }),
                    ])
                    ->columns(4),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revoke')
                ->label(__('Revoke Token'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Revoke API Token'))
                ->modalDescription(__('Are you sure you want to revoke this token? This action cannot be undone and the token will immediately stop working.'))
                ->action(function (): void {
                    /** @var PersonalAccessToken $record */
                    $record = $this->record;

                    /** @var User|null $tokenOwner */
                    $tokenOwner = $record->tokenable;

                    if ($tokenOwner !== null) {
                        $service = app(ApiTokenService::class);
                        $service->revokeToken($tokenOwner, $record->id);

                        Notification::make()
                            ->success()
                            ->title(__('Token Revoked'))
                            ->body(__('The API token has been revoked successfully.'))
                            ->send();

                        $this->redirect($this->getResource()::getUrl('index'));
                    }
                }),
        ];
    }
}
