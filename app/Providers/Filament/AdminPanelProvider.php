<?php

namespace App\Providers\Filament;

use App\Domains\System\Models\Setting;
use App\Filament\Pages\Dashboard;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Livewire;

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
            ->favicon(asset('favicon.svg'))
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15rem')
            ->collapsedSidebarWidth('4.75rem')
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(\App\Filament\Auth\Pages\Login::class)
            ->colors([
                'primary' => Color::hex('#4141d7'),
            ])
            ->renderHook(
                PanelsRenderHook::SIDEBAR_LOGO_BEFORE,
                function () {
                    $brandName = e(filament()->getBrandName());
                    $homeUrl = e(filament()->getHomeUrl() ?? url('/admin'));
                    $environment = e(app()->environment());
                    $isProduction = app()->environment('production');
                    $dotClasses = 'hl-brand-dot'.($isProduction ? ' hl-live' : '');

                    return <<<HTML
                    <div class="hl-sidebar-brand">
                        <button
                            type="button"
                            class="hl-sidebar-toggle hl-sidebar-toggle-expand"
                            aria-label="Expand sidebar"
                            title="Expand sidebar"
                            x-show="! \$store.sidebar.isOpen"
                            x-on:click="\$store.sidebar.open()"
                            x-cloak
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 7h16M4 12h16M4 17h16"/>
                            </svg>
                        </button>

                        <a href="{$homeUrl}" class="hl-brand-lockup" x-show="\$store.sidebar.isOpen" x-cloak>
                            <span class="hl-brand-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 3 2 8l10 5 10-5-10-5Z"/>
                                    <path d="M5 10v5c0 1.5 3.1 3 7 3s7-1.5 7-3v-5"/>
                                </svg>
                            </span>
                            <span class="hl-brand-copy">
                                <span class="hl-brand-name">{$brandName}</span>
                                <span class="hl-brand-subtitle">
                                    <span>admin</span>
                                    <span class="{$dotClasses}"></span>
                                    <span>{$environment}</span>
                                </span>
                            </span>
                        </a>

                        <button
                            type="button"
                            class="hl-sidebar-toggle hl-sidebar-toggle-collapse"
                            aria-label="Collapse sidebar"
                            title="Collapse sidebar"
                            x-show="\$store.sidebar.isOpen"
                            x-on:click="\$store.sidebar.close()"
                            x-cloak
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M9 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4M14 8l-4 4 4 4M10 12h11"/>
                            </svg>
                        </button>
                    </div>
                    HTML;
                }
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_LOGO_AFTER,
                function () {
                    $pageClass = request()->route()?->getAction('controller');
                    $title = (is_string($pageClass) && class_exists($pageClass) && method_exists($pageClass, 'getNavigationLabel'))
                        ? $pageClass::getNavigationLabel()
                        : Str::headline(Str::afterLast(request()->route()?->getName() ?? 'Dashboard', '.'));

                    $slug = e(Str::slug(filament()->getBrandName()));
                    $title = e($title);

                    return <<<HTML
                    <div class="hl-breadcrumb">
                        <span class="hl-breadcrumb-slug">{$slug}</span>
                        <span class="hl-breadcrumb-sep">/</span>
                        <span class="hl-breadcrumb-title">{$title}</span>
                    </div>
                    HTML;
                }
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                function () {
                    $rate = (float) Setting::get('default_commission_percentage', 20);
                    $url = url('/admin/settings') . '#finance';

                    return <<<HTML
                    <a href="{$url}" title="Platform commission rate — click to configure" class="hl-commission-pill">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                        Commission {$rate}%
                    </a>
                    HTML;
                }
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn () => Livewire::mount('admin-notification-bell')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => <<<'HTML'
                <script>
                    var _hlNavScroll  = 0;
                    var _hlNavFrozen  = false;
                    var _hlWatching   = false;
                    var _hlGroupTimer = null;
                    var _hlRafId      = null;

                    function hlGetNav() {
                        var navs = document.querySelectorAll('.fi-sidebar-nav');
                        for (var i = 0; i < navs.length; i++) {
                            if (navs[i].offsetHeight > 0) return navs[i];
                        }
                        return navs[0] || null;
                    }

                    function hlAttach(nav) {
                        if (!nav || nav._hlA) return;
                        nav._hlA = true;
                        nav.addEventListener('scroll', function () {
                            if (!_hlNavFrozen) _hlNavScroll = this.scrollTop;
                        }, { passive: true });
                    }

                    // rAF loop: keep forcing scrollTop on every frame while navigating
                    function hlRestoreLoop() {
                        if (!_hlNavFrozen) return;
                        var nav = hlGetNav();
                        if (nav) nav.scrollTop = _hlNavScroll;
                        _hlRafId = requestAnimationFrame(hlRestoreLoop);
                    }

                    function hlEnsureVisible() {
                        var nav    = hlGetNav();
                        var active = nav && nav.querySelector('.fi-sidebar-item.fi-active');
                        if (!nav || !active) return;
                        var nr = nav.getBoundingClientRect();
                        var ir = active.getBoundingClientRect();
                        if (ir.top < nr.top) {
                            nav.scrollTop -= (nr.top - ir.top) + 4;
                        } else if (ir.bottom > nr.bottom) {
                            nav.scrollTop += (ir.bottom - nr.bottom) + 4;
                        }
                    }

                    function hlWatchGroups() {
                        if (_hlWatching) return;
                        var nav = hlGetNav();
                        if (!nav) return;
                        _hlWatching = true;
                        new MutationObserver(function (mutations) {
                            if (_hlNavFrozen) return;
                            for (var i = 0; i < mutations.length; i++) {
                                if (mutations[i].target.classList.contains('fi-sidebar-group')) {
                                    clearTimeout(_hlGroupTimer);
                                    _hlGroupTimer = setTimeout(hlEnsureVisible, 220);
                                    return;
                                }
                            }
                        }).observe(nav, { attributes: true, attributeFilter: ['class'], subtree: true });
                    }

                    // Capture scroll + start rAF loop on the click itself
                    document.addEventListener('click', function (e) {
                        if (e.target.closest('.fi-sidebar-item a')) {
                            var nav = hlGetNav();
                            if (nav) {
                                _hlNavScroll = nav.scrollTop;
                                _hlNavFrozen = true;
                                cancelAnimationFrame(_hlRafId);
                                hlRestoreLoop();
                            }
                        }
                    }, true);

                    document.addEventListener('livewire:navigate', function () {
                        _hlNavFrozen = true; // freeze for non-click navigations too
                    });

                    function hlSidebarFocus() {
                        var store = window.Alpine && window.Alpine.store('sidebar');
                        var expanded = false;

                        if (store) {
                            var ag  = document.querySelector('.fi-sidebar-group.fi-active');
                            var lbl = ag && ag.dataset.groupLabel;
                            if (lbl && (store.collapsedGroups || []).includes(lbl)) {
                                store.collapsedGroups = store.collapsedGroups.filter(function (g) { return g !== lbl; });
                                expanded = true;
                            }
                        }

                        setTimeout(function () {
                            cancelAnimationFrame(_hlRafId); // stop the restore loop
                            var nav = hlGetNav();
                            if (nav) nav.scrollTop = _hlNavScroll; // final restore
                            hlEnsureVisible();
                            _hlNavFrozen = false;
                            hlAttach(nav);
                            hlWatchGroups();
                        }, expanded ? 260 : 100);
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        hlAttach(hlGetNav());
                        setTimeout(function () { hlSidebarFocus(); hlWatchGroups(); }, 150);
                    });
                    document.addEventListener('livewire:navigated', hlSidebarFocus);
                </script>
                HTML
            )
            ->navigationGroups([
                NavigationGroup::make('Overview')->collapsible(),
                NavigationGroup::make('Learning')->collapsible(),
                NavigationGroup::make('Commerce')->collapsible(),
                NavigationGroup::make('People')->collapsible(),
                NavigationGroup::make('Finance')->collapsible(),
                NavigationGroup::make('Reports')->collapsible(),
                NavigationGroup::make('System')->collapsible(),
                NavigationGroup::make('Security')->collapsible(),
                NavigationGroup::make('Monitoring')->collapsible(),
            ])
            ->navigationItems([
                NavigationItem::make('Horizon')
                    ->url('/horizon')
                    ->icon('heroicon-o-chart-bar-square')
                    ->group('Monitoring')
                    ->sort(3)
                    ->visible(fn () => auth()->user()?->hasRole(['super-admin'])),
            ])
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
            ]);
    }
}
