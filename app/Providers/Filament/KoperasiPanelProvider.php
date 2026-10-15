<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class KoperasiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('koperasi')
            ->path('koperasi')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->registration(\App\Filament\Pages\Auth\RegisterKoperasi::class)
            ->brandName('SIFITKOP Koperasi')
            ->font('Inter')
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="' . asset('css/spiggle-theme.css') . '?v=' . filemtime(public_path('css/spiggle-theme.css')) . '" />'
            )
            ->renderHook(
<<<<<<< HEAD
               \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER,
=======
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER,
>>>>>>> 67efacadfa6f074c57cb346c7380c20a5f378508
                fn (): string => '<div x-data="{ 
                        theme: localStorage.getItem(\'theme\') || \'light\',
                        toggle() {
                            this.theme = this.theme === \'dark\' ? \'light\' : \'dark\';
                            localStorage.setItem(\'theme\', this.theme);
                            if (this.theme === \'dark\') {
                                document.documentElement.classList.add(\'dark\');
                            } else {
                                document.documentElement.classList.remove(\'dark\');
                            }
                        }
                    }" 
                    x-on:click="toggle()"
                    class="spiggle-theme-switcher">
                    <span class="spiggle-theme-switcher-text">Dark Mode</span>
                    <div class="spiggle-toggle"><div class="spiggle-toggle-dot"></div></div>
                </div>'
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::TOPBAR_START,
                fn (): string => '<div x-data="{
                        breadcrumbs: [],
                        updateBreadcrumbs() {
                            setTimeout(() => {
                                let breadcrumbEl = document.querySelector(\'.fi-main .fi-breadcrumbs\');
                                if (breadcrumbEl) {
                                    let links = Array.from(breadcrumbEl.querySelectorAll(\'li > a, li > span\'));
                                    this.breadcrumbs = links.map(el => el.innerText.trim()).filter(text => text.length > 0);
                                } else {
                                    let h1 = document.querySelector(\'.fi-header-heading\');
                                    this.breadcrumbs = h1 ? [h1.innerText.trim()] : [document.title.split(\' - \')[0]];
                                }
                            }, 50);
                        }
                    }" 
                    x-init="
                        updateBreadcrumbs();
                        document.addEventListener(\'livewire:navigated\', () => updateBreadcrumbs());
                        new MutationObserver(() => updateBreadcrumbs()).observe(document.querySelector(\'title\'), { childList: true });
                    "
                    class="hidden lg:flex items-center gap-x-2 text-sm font-medium text-gray-500 ml-4 dark:text-gray-400"
                >
                    <span class="text-primary-600 dark:text-primary-400 font-bold">Portal Koperasi</span>
                    
                    <template x-for="(crumb, index) in breadcrumbs" :key="index">
                        <div class="flex items-center gap-x-2">
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            <span x-text="crumb" :class="index === breadcrumbs.length - 1 ? \'font-semibold text-gray-700 dark:text-gray-200\' : \'text-gray-500\'"></span>
                        </div>
                    </template>
                </div>'
            )
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Koperasi/Resources'), for: 'App\\Filament\\Koperasi\\Resources')
            ->discoverPages(in: app_path('Filament/Koperasi/Pages'), for: 'App\\Filament\\Koperasi\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Koperasi/Widgets'), for: 'App\\Filament\\Koperasi\\Widgets')
            ->widgets([
                \App\Filament\Koperasi\Widgets\KoperasiStatusWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
            ->databaseNotifications();
    }
}
