<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class PdpaDashboard extends Page
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?string $navigationLabel = null;

    protected static ?string $slug = 'pdpa/dashboard';

    protected string $view = 'filament.pages.pdpa-dashboard';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user() ?? auth()->user();

        return (bool) ($user?->hasAdminAccess());
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.pdpa_dashboard.label');
    }
}
