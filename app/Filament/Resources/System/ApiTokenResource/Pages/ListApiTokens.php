<?php

declare(strict_types=1);

namespace App\Filament\Resources\System\ApiTokenResource\Pages;

use App\Filament\Resources\System\ApiTokenResource;
use App\Models\User;
use App\Services\ApiTokenService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * List API Tokens Page
 *
 * Displays paginated API tokens with filtering and management capabilities.
 * Admin and superuser access only.
 *
 * @trace Requirements 37.1, 37.2, 37.3
 */
class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('API Tokens');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_token')
                ->label(__('Create Token'))
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('user_id')
                        ->label(__('User'))
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->helperText(__('Select the user who will own this token.')),

                    Forms\Components\TextInput::make('name')
                        ->label(__('Token Name'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('e.g., Production API, Mobile App'))
                        ->helperText(__('A descriptive name to identify this token.')),

                    Forms\Components\CheckboxList::make('abilities')
                        ->label(__('Token Abilities'))
                        ->options([
                            'read:tickets' => __('Read Tickets - View helpdesk tickets'),
                            'write:tickets' => __('Write Tickets - Create/update helpdesk tickets'),
                            'read:loans' => __('Read Loans - View loan applications'),
                            'write:loans' => __('Write Loans - Create/update loan applications'),
                            'admin:all' => __('Admin All - Full administrative access'),
                        ])
                        ->default(['read:tickets', 'read:loans'])
                        ->columns(1)
                        ->helperText(__('Select the permissions this token should have.')),

                    Forms\Components\Select::make('expiration_days')
                        ->label(__('Token Expiration'))
                        ->options([
                            7 => __('7 days'),
                            30 => __('30 days (default)'),
                            90 => __('90 days'),
                            180 => __('180 days'),
                            365 => __('1 year'),
                            0 => __('Never expires'),
                        ])
                        ->default(30)
                        ->required()
                        ->helperText(__('How long until this token expires.')),
                ])
                ->action(function (array $data): void {
                    /** @var User|null $user */
                    $user = User::find($data['user_id']);

                    if ($user === null) {
                        Notification::make()
                            ->danger()
                            ->title(__('Error'))
                            ->body(__('User not found.'))
                            ->send();

                        return;
                    }

                    $service = app(ApiTokenService::class);

                    $abilities = $data['abilities'] ?? ['*'];
                    $expirationDays = (int) $data['expiration_days'];

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
                })
                ->modalHeading(__('Create New API Token'))
                ->modalDescription(__('Create a new API token with specific abilities and expiration period.'))
                ->modalSubmitActionLabel(__('Create Token')),

            Action::make('revoke_expired')
                ->label(__('Revoke Expired'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Revoke All Expired Tokens'))
                ->modalDescription(__('This will permanently delete all expired API tokens. This action cannot be undone.'))
                ->action(function (): void {
                    $count = PersonalAccessToken::where('tokenable_type', User::class)
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<', Carbon::now())
                        ->delete();

                    Notification::make()
                        ->success()
                        ->title(__('Expired Tokens Revoked'))
                        ->body(__(':count expired tokens have been revoked.', ['count' => $count]))
                        ->send();
                }),

            Action::make('usage_stats')
                ->label(__('Usage Statistics'))
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalHeading(__('API Token Usage Statistics'))
                ->modalContent(function (): \Illuminate\Contracts\View\View {
                    /** @var User|null $currentUser */
                    $currentUser = Auth::user();

                    $totalTokens = PersonalAccessToken::where('tokenable_type', User::class)->count();
                    $activeTokens = PersonalAccessToken::where('tokenable_type', User::class)
                        ->where(function ($query): void {
                            $query->whereNull('expires_at')
                                ->orWhere('expires_at', '>', Carbon::now());
                        })
                        ->count();
                    $expiredTokens = PersonalAccessToken::where('tokenable_type', User::class)
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<', Carbon::now())
                        ->count();
                    $expiringSoon = PersonalAccessToken::where('tokenable_type', User::class)
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '>', Carbon::now())
                        ->where('expires_at', '<=', Carbon::now()->addDays(7))
                        ->count();

                    return view('filament.modals.api-token-stats', [
                        'totalTokens' => $totalTokens,
                        'activeTokens' => $activeTokens,
                        'expiredTokens' => $expiredTokens,
                        'expiringSoon' => $expiringSoon,
                    ]);
                })
                ->modalActions([
                    Action::make('close')
                        ->label(__('Close'))
                        ->color('gray'),
                ]),
        ];
    }
}
