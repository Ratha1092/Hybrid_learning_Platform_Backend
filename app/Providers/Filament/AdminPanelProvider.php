<?php

namespace App\Providers\Filament;

use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Enums\ThemeMode;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Dashboard;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
                ->defaultThemeMode(ThemeMode::Dark)
            ->brandName('Hybrid Learning')
            ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('15rem')
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(\App\Filament\Auth\Pages\Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->databaseNotifications(true, null, true, DatabaseNotificationsPosition::Topbar)
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])

            // ─── HAMBURGER TOGGLE (no new files) ───
            ->renderHook(
                'panels::head.end',
                fn (): string => <<<'HTML'
                    <style>
                        html .fi-main-sidebar.fi-sidebar,
                        html .fi-sidebar,
                        html .fi-sidebar.fi-sidebar-open,
                        html .fi-sidebar:not(.fi-sidebar-open),
                        html .fi-sidebar:where(.dark,.dark *) {
                            background: radial-gradient(circle at 18% 6%, rgba(94,144,255,0.16), transparent 30%) !important;
                            background-image: linear-gradient(180deg,#0b1322 0%,#08101c 55%,#09121f 100%) !important;
                            background-color: transparent !important;
                            border-right: 1px solid rgba(255,255,255,0.08) !important;
                            box-shadow: 20px 0 60px rgba(0,0,0,0.32) !important;
                            color: #eef5ff !important;
                        }

                        html .fi-main-sidebar .fi-sidebar-inner,
                        html .fi-sidebar-inner {
                            background: transparent !important;
                        }

                        html .fi-main-sidebar .fi-sidebar-header,
                        html .fi-sidebar-header,
                        html .fi-sidebar:where(.dark,.dark *) .fi-sidebar-header {
                            background: rgba(255,255,255,0.03) !important;
                            backdrop-filter: blur(12px) !important;
                            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
                        }

                        html .fi-sidebar-item-btn,
                        html .fi-sidebar-item-button,
                        html .fi-sidebar .fi-sidebar-item-btn,
                        html .fi-sidebar .fi-sidebar-item-button {
                            background: rgba(255,255,255,0.02) !important;
                            border: 1px solid rgba(255,255,255,0.06) !important;
                            color: rgba(255,255,255,0.78) !important;
                            border-radius: 12px !important;
                        }
                    </style>
                HTML
            )
            ->renderHook(
                'panels::body.end',
                fn (): string => '
                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const btn = document.querySelector(".fi-topbar-open-sidebar-btn");
                        if (!btn) return;

                        btn.addEventListener("click", (e) => {
                            const store = window.Alpine?.store("sidebar");
                            if (!store) return;

                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();

                            if (store.isOpen || store.opened) store.close();
                            else store.open();
                        }, true);
                    });
                    </script>
                '
            );
    }
}