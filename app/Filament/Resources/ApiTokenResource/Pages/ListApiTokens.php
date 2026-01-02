<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

/**
 * List API Tokens Page
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.4
 *
 * @version 3.5.0
 */
class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    public ?string $newApiToken = null;

    public function mount(): void
    {
        parent::mount();

        // Check for newly created token in session
        if (session()->has('new_api_token')) {
            $this->newApiToken = session('new_api_token');
            session()->forget('new_api_token');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('api_tokens.actions.create')),
        ];
    }

    public function getTitle(): string
    {
        return __('api_tokens.plural_model_label');
    }

    public function dismissTokenBanner(): void
    {
        $this->newApiToken = null;
    }

    public function getHeader(): ?View
    {
        if ($this->newApiToken) {
            return view('filament.components.token-reveal-banner', [
                'token' => $this->newApiToken,
            ]);
        }

        return null;
    }
}
