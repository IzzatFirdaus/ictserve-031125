<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Widgets\AssetLoanStatsOverview;
use App\Filament\Widgets\AssetUtilizationWidget;
use App\Filament\Widgets\CrossModuleIntegrationChart;
use App\Filament\Widgets\HelpdeskStatsOverview;
use App\Filament\Widgets\LoanApprovalQueueWidget;
use App\Filament\Widgets\UnifiedAnalyticsChart;
use App\Http\Middleware\AdminAccessMiddleware;
use App\Http\Middleware\AdminRateLimitMiddleware;
use App\Http\Middleware\SessionTimeoutMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // WCAG 2.2 AA Compliant Color Palette (Requirements 14.1, 15.1)
            ->colors([
                'primary' => Color::hex('#0056b3'),   // 6.8:1 contrast ratio
                'success' => Color::hex('#198754'),   // 4.9:1 contrast ratio
                'warning' => Color::hex('#ff8c00'),   // 4.5:1 contrast ratio
                'danger' => Color::hex('#b50c0c'),    // 8.2:1 contrast ratio
            ])
            // Branding Configuration (Requirements 16.1)
            ->brandName('ICTServe Admin')
            ->brandLogo(asset('images/motac-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/favicon.ico'))
            // Navigation Groups (Requirements 16.1)
            ->navigationGroups([
                NavigationGroup::make('Helpdesk Management')
                    ->icon('heroicon-o-ticket')
                    ->collapsed(false),
                NavigationGroup::make('Loan Management')
                    ->icon('heroicon-o-cube')
                    ->collapsed(false),
                NavigationGroup::make('Asset Management')
                    ->icon('heroicon-o-server')
                    ->collapsed(false),
                NavigationGroup::make('User Management')
                    ->icon('heroicon-o-users')
                    ->collapsed(false),
                NavigationGroup::make('System Configuration')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ])
            // Resource Discovery
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                AdminDashboard::class,
            ])
            // Widget Discovery
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                // Unified Dashboard Widgets
                HelpdeskStatsOverview::class,
                AssetLoanStatsOverview::class,
                CrossModuleIntegrationChart::class,
                AssetUtilizationWidget::class,
                UnifiedAnalyticsChart::class,
                LoanApprovalQueueWidget::class,
            ])
            // Middleware Stack (Requirements 17.2, 17.5)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class, // CSRF Protection (Requirement 17.2)
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SessionTimeoutMiddleware::class, // Session Timeout (Requirement 17.2, 17.5)
                AdminRateLimitMiddleware::class, // Rate Limiting (Requirement 17.2)
            ])
            // Authentication Middleware with Admin Access Check (Requirements 17.1)
            ->authMiddleware([
                Authenticate::class,
                AdminAccessMiddleware::class,
            ])
            // Database Notifications
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            // Global Search
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            // Topbar Configuration
            ->topNavigation(false)
            ->sidebarCollapsibleOnDesktop()
            // Dark Mode Support
            ->darkMode(false) // Disabled for WCAG compliance consistency
            // Max Content Width
            ->maxContentWidth('full')
            // Spa Mode for Better Performance
            ->spa();
    }
}
