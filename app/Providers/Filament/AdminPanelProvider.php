<?php

declare(strict_types=1);

// # trace: .kiro/specs/frontend-pages-redesign/design.md §Admin Branding

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Http\Middleware\AdminAccessMiddleware;
use App\Http\Middleware\AdminRateLimitMiddleware;
use App\Http\Middleware\RedirectAliasResources;
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
        // Add skip link for accessibility (WCAG 2.2 AA)
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn (): string => <<<'HTML'
                <!-- Skip Link for Keyboard Navigation (D14 §10.2) -->
                <a href="#main-content"
                   class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4
                          focus:z-50 focus:rounded-lg focus:bg-primary-600 focus:px-4 focus:py-2
                          focus:text-white focus:shadow-dropdown focus:outline-none
                          focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                   tabindex="1">
                    {{ __('Langkau ke kandungan utama') }}
                </a>

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
                <!-- Accessibility Meta Tags (WCAG 2.2 AA) -->
                <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
                <meta name="theme-color" content="#0056b3">
                <meta name="description" content="ICTServe Admin - Sistem Pengurusan Perkhidmatan ICT MOTAC">

                <!-- MyDS Typography System (D13 §2.4) -->
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">                                                                                                     
                <style>
                    /* MyDS Typography System Implementation (D13 §2.4) */
                    :root {
                        --font-heading: 'Poppins', system-ui, sans-serif;
                        --font-body: 'Inter', system-ui, sans-serif;
                        --font-mono: 'JetBrains Mono', monospace;

                        /* MyDS Color Tokens (D14 §4.1.1) */
                        --color-primary: #0056b3;
                        --color-primary-hover: #004494;
                        --color-primary-light: #e6f0ff;
                        --color-text-primary: #1a1a1a;
                        --color-text-secondary: #4a4a4a;
                        --color-success: #198754;
                        --color-warning: #ff8c00;
                        --color-danger: #b50c0c;
                        --color-focus-ring: #0056b3;

                        /* MyDS Shadow System (D14 §7.5) */
                        --shadow-button: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);
                        --shadow-card: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);
                        --shadow-dropdown: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);

                        /* MyDS Motion System (D14 §7.6) */
                        --motion-easeout: cubic-bezier(0, 0, 0.58, 1);
                        --motion-easeoutback: cubic-bezier(0.4, 1.4, 0.2, 1);
                        --duration-short: 200ms;
                        --duration-medium: 400ms;
                        --duration-long: 600ms;
                    }

                    /* Apply MyDS Typography */
                    .fi-header-heading,
                    .fi-section-header-heading,
                    .fi-modal-heading,
                    h1, h2, h3, h4, h5, h6 {
                        font-family: var(--font-heading) !important;
                    }

                    .fi-body,
                    .fi-main,
                    body {
                        font-family: var(--font-body) !important;
                    }

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

                    /* Main content anchor for skip link */
                    #main-content {
                        scroll-margin-top: 2rem;
                    }

                    /* Enhanced focus indicators for all interactive elements (WCAG 2.2 AA) */
                    .fi-btn:focus-visible,
                    .fi-ta-btn:focus-visible,
                    .fi-dropdown-trigger:focus-visible,
                    .fi-sidebar-nav-item:focus-visible,
                    .fi-tabs-tab:focus-visible,
                    .fi-form-field-wrapper input:focus-visible,
                    .fi-form-field-wrapper select:focus-visible,
                    .fi-form-field-wrapper textarea:focus-visible {
                        outline: 3px solid var(--color-focus-ring) !important;
                        outline-offset: 2px !important;
                        box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.3) !important;
                    }

                    /* MyDS Motion System Implementation */
                    .fi-btn,
                    .fi-ta-btn,
                    .fi-dropdown-trigger,
                    .fi-sidebar-nav-item {
                        transition: all var(--duration-short) var(--motion-easeout);
                    }

                    /* MyDS Shadow System Implementation */
                    .fi-section,
                    .fi-widget,
                    .fi-card {
                        box-shadow: var(--shadow-card);
                    }

                    .fi-btn {
                        box-shadow: var(--shadow-button);
                    }

                    .fi-dropdown-panel,
                    .fi-modal {
                        box-shadow: var(--shadow-dropdown);
                    }

                    /* Dashboard Grid Improvements (D14 §7.4) */
                    .fi-dashboard-widgets {
                        display: grid;
                        gap: 1.5rem;
                        grid-template-columns: repeat(4, minmax(0, 1fr));
                    }

                    @media (max-width: 767px) {
                        .fi-dashboard-widgets {
                            grid-template-columns: repeat(4, minmax(0, 1fr));
                            gap: 1.125rem;
                            padding: 1.125rem;
                        }
                    }

                    @media (min-width: 768px) and (max-width: 1023px) {
                        .fi-dashboard-widgets {
                            grid-template-columns: repeat(8, minmax(0, 1fr));
                            gap: 1.5rem;
                            padding: 1.5rem;
                        }
                    }

                    @media (min-width: 1024px) {
                        .fi-dashboard-widgets {
                            grid-template-columns: repeat(12, minmax(0, 1fr));
                            gap: 1.5rem;
                            max-width: 80rem;
                            margin: 0 auto;
                        }
                    }

                    /* Widget Responsive Behavior */
                    .fi-wi-stats-overview {
                        grid-column: span 4;
                    }

                    @media (min-width: 768px) {
                        .fi-wi-stats-overview {
                            grid-column: span 8;
                        }
                    }

                    @media (min-width: 1024px) {
                        .fi-wi-stats-overview {
                            grid-column: span 12;
                        }
                    }

                    /* Chart widgets responsive */
                    .fi-wi-chart {
                        grid-column: span 4;
                    }

                    @media (min-width: 768px) {
                        .fi-wi-chart {
                            grid-column: span 4;
                        }
                    }

                    @media (min-width: 1024px) {
                        .fi-wi-chart {
                            grid-column: span 6;
                        }
                    }

                    /* Full-width widgets */
                    .fi-wi-full-width {
                        grid-column: span 4;
                    }

                    @media (min-width: 768px) {
                        .fi-wi-full-width {
                            grid-column: span 8;
                        }
                    }

                    @media (min-width: 1024px) {
                        .fi-wi-full-width {
                            grid-column: span 12;
                        }
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
            function (): string {
                return (string) \Illuminate\Support\Facades\View::make('filament.components.portal-link');
            },
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->login(\App\Filament\Pages\Auth\Login::class)
            // MOTAC Branding Theme (D12-D14 UI/UX Design Guidelines)
            // Custom theme CSS for WCAG 2.2 AA compliance and MyGOV standards
            ->viteTheme('resources/css/filament/admin/theme.css')
            // WCAG 2.2 AA Compliant Color Palette (D14 §4.1 Color System)
            ->colors([
                'primary' => Color::hex('#0056b3'),   // MyGOV Blue - 6.8:1 contrast ratio
                'success' => Color::hex('#198754'),   // MyGOV Green - 4.9:1 contrast ratio
                'warning' => Color::hex('#ff8c00'),   // MyGOV Orange - 4.5:1 contrast ratio
                'danger' => Color::hex('#b50c0c'),    // MyGOV Red - 8.2:1 contrast ratio
                'gray' => Color::hex('#64748b'),      // Slate - Matching MyDS
                'info' => Color::hex('#3b82f6'),      // Blue - 4.5:1 contrast ratio
            ])
            // Branding Configuration (D12 §5.1 MOTAC Branding)
            ->brandName(__('filament.navigation.brand_name'))
            ->brandLogo(asset('images/motac-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->darkModeBrandLogo(asset('images/motac-logo.png'))
            ->favicon(asset('favicon.ico'))
            // Navigation Groups (D12 §6.1 Navigation Structure - Bahasa Melayu)
            ->navigationGroups([
                NavigationGroup::make(__('filament.navigation.operations'))
                    ->icon('heroicon-o-briefcase')
                    ->collapsed(false),
                NavigationGroup::make(__('filament.navigation.management'))
                    ->icon('heroicon-o-users')
                    ->collapsed(false),
                NavigationGroup::make(__('filament.navigation.system'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
                NavigationGroup::make(__('filament.navigation.reports'))
                    ->icon('heroicon-o-chart-bar')
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
            ])
            // Middleware Stack (Requirements 17.2, 17.5)
            ->middleware([
                RedirectAliasResources::class, // Redirect deprecated alias URLs (Task 36)
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
