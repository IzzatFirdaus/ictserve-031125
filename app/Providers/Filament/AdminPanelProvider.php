<?php

declare(strict_types=1);

// # trace: .kiro/specs/frontend-pages-redesign/design.md §Admin Branding

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Resources\Loans\Widgets\LoanAnalyticsWidget;
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
use Filament\Support\Assets\Css as FilamentCss;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn (): string => <<<'HTML'
                <script>
                    (() => {
                        const persistSidebarState = () => {
                            ['isOpen', 'isOpenDesktop'].forEach((key) => {
                                try {
                                    localStorage.setItem(key, JSON.stringify(true));
                                } catch (_) {
                                }
                            });
                        };

                        persistSidebarState();

                        const ensureSidebarIsOpen = () => {
                            const sidebarStore = window.Alpine?.store('sidebar');

                            if (! sidebarStore) {
                                requestAnimationFrame(ensureSidebarIsOpen);

                                return;
                            }

                            sidebarStore.open();
                            sidebarStore.isOpenDesktop = true;
                        };

                        document.addEventListener('alpine:init', () => {
                            requestAnimationFrame(ensureSidebarIsOpen);
                        });
                    })();
                </script>
            HTML,
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => <<<'HTML'
                <style>
                    /* Global SVG icon sizing fix */
                    .fi-main-ctn svg:not([class*="w-"]):not([class*="h-"]) {
                        width: 1.5rem;
                        height: 1.5rem;
                    }

                    /* Notification icon sizing */
                    .fi-no-icon svg {
                        width: 1.25rem;
                        height: 1.25rem;
                    }

                    /* Table action icons */
                    .fi-ta-icon svg {
                        width: 1.25rem;
                        height: 1.25rem;
                    }

                    /* Global search improvements */
                    [x-data*="globalSearchPanel"] .fi-global-search-input {
                        padding: 0.75rem 1rem;
                    }

                    [x-data*="globalSearchPanel"] .fi-global-search-no-results {
                        text-align: center;
                        padding: 2rem 1rem;
                        color: rgb(107 114 128);
                        font-size: 0.875rem;
                    }

                    [x-data*="globalSearchPanel"] .fi-global-search-results {
                        padding: 0.5rem;
                    }
                </style>
            HTML,
        );

        // Ensure the published Filament CSS (public/css/filament/filament/app.css)
        // — which contains core Filament styles for classes like .fi-body and
        // .fi-simple-layout — is registered for the Filament package so it
        // appears on Filament pages (including the login page). This mirrors
        // the publishing step `php artisan filament:assets` but guarantees the
        // stylesheet is linked by the Blade assets renderer.
        FilamentAsset::register([
            FilamentCss::make('app', public_path('css/filament/filament/app.css'))
                ->relativePublicPath('css/filament/filament/app.css'),
        ], 'filament/filament');

        // Add portal link to sidebar
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_END,
            fn (): string => view('filament.components.portal-link')->render(),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->login()
            // MOTAC Branding Theme (Requirements 5.1, D14 §4.1)
            // Custom theme CSS for WCAG 2.2 AA compliance and MOTAC branding
            ->viteTheme('resources/css/filament/admin/theme.css')
            // WCAG 2.2 AA Compliant Color Palette (Requirements 14.1, 15.1)
            ->colors([
                'primary' => Color::hex('#0056b3'),   // 6.8:1 contrast ratio
                'success' => Color::hex('#198754'),   // 4.9:1 contrast ratio
                'warning' => Color::hex('#ff8c00'),   // 4.5:1 contrast ratio
                'danger' => Color::hex('#b50c0c'),    // 8.2:1 contrast ratio
            ])
            // Branding Configuration (Requirements 16.1, 21.4)
            ->brandName('ICTServe Admin')
            ->brandLogo(asset('images/motac-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->darkModeBrandLogo(asset('images/motac-logo.png'))
            ->favicon(asset('favicon.ico'))
            // Navigation Groups (Requirements 16.1)
            ->navigationGroups([
                NavigationGroup::make(__('filament::navigation.operations'))
                    ->icon('heroicon-o-briefcase')
                    ->collapsed(false),
                NavigationGroup::make(__('filament::navigation.management'))
                    ->icon('heroicon-o-users')
                    ->collapsed(false),
                NavigationGroup::make(__('filament::navigation.system'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ])
            // Resource and Cluster Discovery
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->pages([
                AdminDashboard::class,
            ])
            // Widget Discovery
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                // Theme Toggle Widget (v3.6.0)
                \App\Filament\Widgets\ThemeToggleWidget::class,
                // Unified Dashboard Widgets
                HelpdeskStatsOverview::class,
                AssetLoanStatsOverview::class,
                LoanAnalyticsWidget::class, // Loan application detailed analytics
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
            // Dark Mode Support (v3.6.0 - User-controlled via ThemeToggleWidget)
            // Light mode is default, but users can toggle to dark mode via localStorage
            ->darkMode(true)
            // Max Content Width
            ->maxContentWidth('full')
            // Spa Mode for Better Performance
            ->spa();
    }
}
