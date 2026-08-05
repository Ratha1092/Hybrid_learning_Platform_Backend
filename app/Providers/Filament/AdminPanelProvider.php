<?php

namespace App\Providers\Filament;

use App\Domains\System\Models\Setting;
use App\Filament\Pages\Dashboard;
use App\Support\LocalAvatarProvider;
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
            ->defaultAvatarProvider(LocalAvatarProvider::class)
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15rem')
            ->collapsedSidebarWidth('4.75rem')
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(\App\Filament\Auth\Pages\Login::class)
            ->colors([
                'primary' => Color::hex('#2563eb'),
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
                PanelsRenderHook::HEAD_END,
                fn () => '<script>history.scrollRestoration="manual";</script>'
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => <<<'HTML'
                <style>
                    .fi-sidebar-item-btn:focus,
                    .fi-sidebar-item-button:focus,
                    .fi-sidebar-item-btn:focus-visible,
                    .fi-sidebar-item-button:focus-visible {
                        outline: 0 solid transparent !important;
                        --tw-ring-offset-shadow: 0 0 #0000 !important;
                        --tw-ring-shadow: 0 0 #0000 !important;
                    }

                    .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:focus-visible,
                    .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-button:focus-visible {
                        background: rgba(148, 163, 184, 0.07) !important;
                        border-color: rgba(148, 163, 184, 0.14) !important;
                        box-shadow: none !important;
                    }

                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-button,
                    .fi-sidebar-item-btn[aria-current='page'],
                    .fi-sidebar-item-button[aria-current='page'] {
                        border-color: transparent !important;
                        box-shadow: inset 3px 0 0 var(--hl-blue) !important;
                    }
                </style>
                HTML
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => Blade::render('@vite([\'resources/js/admin-echo.js\'])')
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => Blade::render('@vite([\'resources/js/bi-charts.js\'])')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => <<<'HTML'
                <script>
                if (!window._hlSidebarReady) {
                window._hlSidebarReady = true;

                    var _hlNavScroll     = 0;
                    var _hlClickedOffset = null;
                    var _hlWatching      = false;
                    var _hlGroupTimer    = null;
                    var _hlEnfRaf        = null;
                    var _hlEnfTarget     = null;
                    var _hlEnfStop       = 0;
                    var _hlNavGen        = 0;
                    var _hlSidebarOverlay = null;
                    var _hlSidebarOverlayTimer = null;
                    var _hlClickedHref = null;

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
                            if (_hlEnfTarget === null) _hlNavScroll = this.scrollTop;
                        }, { passive: true });
                    }
                    function hlEnforceScroll() {
                        if (performance.now() > _hlEnfStop || _hlEnfTarget === null) {
                            _hlEnfTarget = null;
                            _hlEnfRaf    = null;
                            return;
                        }
                        var nav = hlGetNav();
                        if (nav && nav.scrollTop !== _hlEnfTarget) nav.scrollTop = _hlEnfTarget;
                        _hlEnfRaf = requestAnimationFrame(hlEnforceScroll);
                    }

                    function hlStartEnforce(target, ms) {
                        _hlEnfTarget = Math.max(0, target);
                        _hlEnfStop   = performance.now() + ms;
                        cancelAnimationFrame(_hlEnfRaf);
                        _hlEnfRaf = requestAnimationFrame(hlEnforceScroll);
                    }

                    function hlStopEnforce() {
                        _hlEnfTarget = null;
                        cancelAnimationFrame(_hlEnfRaf);
                        _hlEnfRaf = null;
                    }

                    function hlMakeSidebarCloneInert(clone) {
                        clone.querySelectorAll('script').forEach(function (script) {
                            script.remove();
                        });

                        [clone].concat(Array.prototype.slice.call(clone.querySelectorAll('*'))).forEach(function (node) {
                            Array.prototype.slice.call(node.attributes || []).forEach(function (attribute) {
                                var name = attribute.name;

                                if (
                                    name === 'id' ||
                                    name.indexOf('wire:') === 0 ||
                                    name.indexOf('x-') === 0 ||
                                    name.charAt(0) === '@' ||
                                    name.charAt(0) === ':'
                                ) {
                                    node.removeAttribute(name);
                                }
                            });
                        });

                        clone.setAttribute('aria-hidden', 'true');
                        clone.setAttribute('inert', '');
                    }

                    function hlNormalizeUrl(url) {
                        if (!url) return null;

                        try {
                            var parsed = new URL(url, window.location.origin);
                            return parsed.origin + parsed.pathname + parsed.search;
                        } catch (e) {
                            return null;
                        }
                    }

                    function hlSyncCloneActiveItem(clone) {
                        var href = hlNormalizeUrl(_hlClickedHref);
                        if (!href) return;

                        clone.querySelectorAll('.fi-sidebar-item.fi-active').forEach(function (item) {
                            item.classList.remove('fi-active');
                        });

                        clone.querySelectorAll('.fi-sidebar-item-btn[aria-current], .fi-sidebar-item-button[aria-current]').forEach(function (button) {
                            button.removeAttribute('aria-current');
                        });

                        var links = clone.querySelectorAll('.fi-sidebar-item a[href]');
                        for (var i = 0; i < links.length; i++) {
                            if (hlNormalizeUrl(links[i].href) !== href) continue;

                            var item = links[i].closest('.fi-sidebar-item');
                            if (item) item.classList.add('fi-active');
                            links[i].setAttribute('aria-current', 'page');
                            break;
                        }
                    }

                    function hlFreezeSidebar() {
                        hlThawSidebar();

                        var sidebar = document.querySelector('.fi-sidebar');
                        if (!sidebar || sidebar.offsetWidth === 0 || sidebar.offsetHeight === 0) return;

                        var rect = sidebar.getBoundingClientRect();
                        var clone = sidebar.cloneNode(true);
                        var nav = sidebar.querySelector('.fi-sidebar-nav');
                        var cloneNav = clone.querySelector('.fi-sidebar-nav');

                        if (nav && cloneNav) cloneNav.scrollTop = nav.scrollTop;

                        hlSyncCloneActiveItem(clone);
                        hlMakeSidebarCloneInert(clone);
                        clone.classList.add('hl-sidebar-freeze');

                        Object.assign(clone.style, {
                            position: 'fixed',
                            inset: 'auto auto auto auto',
                            top: rect.top + 'px',
                            left: rect.left + 'px',
                            width: rect.width + 'px',
                            height: rect.height + 'px',
                            margin: '0',
                            zIndex: '60',
                            pointerEvents: 'none',
                            transform: 'none',
                            transition: 'none',
                        });

                        document.body.appendChild(clone);
                        sidebar.dataset.hlPreviousVisibility = sidebar.style.visibility || '';
                        sidebar.style.visibility = 'hidden';
                        sidebar.classList.add('hl-sidebar-under-freeze');
                        _hlSidebarOverlay = clone;
                        clearTimeout(_hlSidebarOverlayTimer);
                        _hlSidebarOverlayTimer = setTimeout(hlThawSidebar, 3000);
                    }

                    function hlThawSidebar() {
                        clearTimeout(_hlSidebarOverlayTimer);
                        _hlSidebarOverlayTimer = null;
                        document.querySelectorAll('.hl-sidebar-under-freeze').forEach(function (sidebar) {
                            sidebar.classList.remove('hl-sidebar-under-freeze');
                            sidebar.style.visibility = sidebar.dataset.hlPreviousVisibility || '';
                            delete sidebar.dataset.hlPreviousVisibility;
                        });
                        if (!_hlSidebarOverlay) return;
                        _hlSidebarOverlay.remove();
                        _hlSidebarOverlay = null;
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
                        _hlNavScroll = nav.scrollTop;
                    }

                    function hlWatchGroups() {
                        if (_hlWatching) return;
                        var nav = hlGetNav();
                        if (!nav) return;
                        _hlWatching = true;
                        new MutationObserver(function (mutations) {
                            if (_hlEnfTarget !== null) return;
                            for (var i = 0; i < mutations.length; i++) {
                                if (mutations[i].target.classList.contains('fi-sidebar-group')) {
                                    clearTimeout(_hlGroupTimer);
                                    _hlGroupTimer = setTimeout(hlEnsureVisible, 220);
                                    return;
                                }
                            }
                        }).observe(nav, { attributes: true, attributeFilter: ['class'], subtree: true });
                    }
                    document.addEventListener('click', function (e) {
                        var itemEl = e.target.closest('.fi-sidebar-item');
                        var linkEl = itemEl && e.target.closest('.fi-sidebar-item a[href], .fi-sidebar-item-btn[href], .fi-sidebar-item-button[href]');
                        if (itemEl && linkEl) {
                            var nav = hlGetNav();
                            if (nav) {
                                var nr = nav.getBoundingClientRect();
                                var ir = itemEl.getBoundingClientRect();
                                _hlNavScroll     = nav.scrollTop;
                                _hlClickedOffset = ir.top - nr.top;
                                _hlClickedHref   = linkEl.href;
                            }
                        }
                    }, true);

                    document.addEventListener('livewire:navigate', function () {
                        document.documentElement.style.setProperty('--hl-sbtn-dur', '0ms');
                        document.documentElement.style.setProperty('--hl-dot-dur', '0ms');
                        document.documentElement.style.setProperty('--hl-sidebar-dur', '0ms');
                        hlFreezeSidebar();
                        // Keep sidebar nav visible during the network round-trip so it
                        // doesn't look like it "reset" while waiting for the new page.
                        var main = document.querySelector('.fi-main');
                        if (main) {
                            main.classList.remove('hl-page-entering');
                            void main.offsetWidth;
                            main.classList.add('hl-page-leaving');
                        }
                    });

                    document.addEventListener('livewire:navigated', function () {
                        var clickedOffset = _hlClickedOffset;
                        _hlClickedOffset  = null;
                        _hlClickedHref = null;
                        _hlNavGen++;
                        var myGen = _hlNavGen;

                        hlPageEnter();

                        // Expand the active group if it was collapsed
                        var expanded = false;
                        var store = window.Alpine && window.Alpine.store('sidebar');
                        if (store && clickedOffset !== null) {
                            var ag  = document.querySelector('.fi-sidebar-group.fi-active');
                            var lbl = ag && ag.dataset.groupLabel;
                            if (lbl && (store.collapsedGroups || []).includes(lbl)) {
                                store.collapsedGroups = store.collapsedGroups.filter(function (g) { return g !== lbl; });
                                expanded = true;
                            }
                        }

                        // Wait for group expansion animation before computing scroll
                        setTimeout(function () {
                            if (myGen !== _hlNavGen) return;
                            var nav    = hlGetNav();
                            if (!nav) return;
                            var active = nav.querySelector('.fi-sidebar-item.fi-active');

                            var target;
                            if (clickedOffset !== null && !expanded && active) {
                                var nr = nav.getBoundingClientRect();
                                var ir = active.getBoundingClientRect();
                                target = nav.scrollTop + (ir.top - nr.top) - clickedOffset;
                            } else {
                                target = _hlNavScroll;
                            }

                            hlStartEnforce(target, expanded ? 60 : 450);

                            setTimeout(function () {
                                if (myGen !== _hlNavGen) return;
                                hlStopEnforce();
                                var nav2 = hlGetNav();
                                if (expanded) hlEnsureVisible();
                                else if (nav2) _hlNavScroll = nav2.scrollTop;
                                hlAttach(nav2);
                                hlWatchGroups();
                                document.documentElement.style.removeProperty('--hl-sbtn-dur');
                                document.documentElement.style.removeProperty('--hl-dot-dur');
                                document.documentElement.style.removeProperty('--hl-sidebar-dur');
                                requestAnimationFrame(function () {
                                    requestAnimationFrame(hlThawSidebar);
                                });
                            }, expanded ? 340 : 500);

                        }, expanded ? 280 : 0);
                    });

                    function hlPageEnter() {
                        var main = document.querySelector('.fi-main');
                        if (!main) return;
                        main.classList.remove('hl-page-leaving', 'hl-page-entering');
                        void main.offsetWidth;
                        main.classList.add('hl-page-entering');
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        hlPageEnter();
                        hlAttach(hlGetNav());
                        setTimeout(function () {
                            var store = window.Alpine && window.Alpine.store('sidebar');
                            if (store) {
                                var ag  = document.querySelector('.fi-sidebar-group.fi-active');
                                var lbl = ag && ag.dataset.groupLabel;
                                if (lbl && (store.collapsedGroups || []).includes(lbl)) {
                                    store.collapsedGroups = store.collapsedGroups.filter(function (g) { return g !== lbl; });
                                }
                            }
                            hlEnsureVisible();
                            hlAttach(hlGetNav());
                            hlWatchGroups();
                        }, 150);
                    });
                    window.rpDate = function(c){var xtr=c.extras||{};function ymd(dt){if(!dt)return'';return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');}function cr(k){if(k==='custom')return['',''];var n=new Date(),y=n.getFullYear(),m=n.getMonth(),d=n.getDate();var dow=n.getDay();var mo=dow===0?6:dow-1;switch(k){case'today':return[ymd(new Date(y,m,d)),ymd(new Date(y,m,d))];case'yesterday':return[ymd(new Date(y,m,d-1)),ymd(new Date(y,m,d-1))];case'this_week':return[ymd(new Date(y,m,d-mo)),ymd(new Date(y,m,d-mo+6))];case'last_week':return[ymd(new Date(y,m,d-mo-7)),ymd(new Date(y,m,d-mo-1))];case'last_30':return[ymd(new Date(y,m,d-29)),ymd(new Date(y,m,d))];case'last_6m':return[ymd(new Date(y,m-6,d)),ymd(new Date(y,m,d))];case'this_month':return[ymd(new Date(y,m,1)),ymd(new Date(y,m+1,0))];case'last_month':return[ymd(new Date(y,m-1,1)),ymd(new Date(y,m,0))];case'this_quarter':{var q=Math.floor(m/3);return[ymd(new Date(y,q*3,1)),ymd(new Date(y,q*3+3,0))];}case'this_year':return[ymd(new Date(y,0,1)),ymd(new Date(y,11,31))];case'all_time':return['',''];default:return[ymd(new Date(y,m,1)),ymd(new Date(y,m+1,0))];}}function nav(url){if(typeof Livewire!=='undefined'&&Livewire.navigate){Livewire.navigate(url);}else{location.href=url;}}return{open:false,preset:c.preset,from:c.from||'',to:c.to||'',fmt:function(s){if(!s)return'—';var dt=new Date(s+'T00:00:00');return dt.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});},get pFrom(){return this.fmt(this.from);},get pTo(){return this.fmt(this.to);},pick:function(k){this.preset=k;if(k!=='custom'){var r=cr(k);this.from=r[0];this.to=r[1];}this.apply();},apply:function(){var p=new URLSearchParams();p.set('preset',this.preset);if(this.preset==='custom'){if(this.from)p.set('date_from',this.from);if(this.to)p.set('date_to',this.to);}var ks=Object.keys(xtr);if(ks.length){var wrap=this.$el.closest('.rp')||document.body;ks.forEach(function(k){var sel=wrap.querySelector('select[wire\\:model\\.live="'+k+'"],select[wire\\:model="'+k+'"]');if(sel&&sel.value&&sel.value!=='all'){p.set(k,sel.value);}});}this.open=false;nav(location.pathname+'?'+p.toString());},reset:function(){this.open=false;nav(location.pathname);},init:function(){var self=this,el=this.$el;this._navH=function(){if(!el.isConnected)return;var p=el.dataset.rpPreset;if(p!==undefined){self.open=false;self.preset=p;self.from=el.dataset.rpFrom||'';self.to=el.dataset.rpTo||'';}};window.addEventListener('livewire:navigated',this._navH);},destroy:function(){window.removeEventListener('livewire:navigated',this._navH);}};};

                } // end window._hlSidebarReady guard
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
