<?php

namespace XiNaaru\McHost24Subdomains\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use XiNaaru\McHost24Subdomains\Livewire\Server\SubdomainManager;

class McHost24SubdomainsPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            plugin_path(
                'mchost24-subdomains',
                'config/mchost24-subdomains.php'
            ),
            'mchost24-subdomains'
        );
    }

    public function boot(): void
    {
        Livewire::component(
            'mchost24-subdomains-server-manager',
            SubdomainManager::class
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_END,
            function (): string {
                return Blade::render(
                    '<livewire:mchost24-subdomains-server-manager />'
                );
            },
            scopes: [
                \App\Filament\Server\Pages\Settings::class,
            ]
        );
    }
}