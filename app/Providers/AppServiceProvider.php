<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): HtmlString => new HtmlString('
            <script>
            console.log("Script loaded");
            
            document.addEventListener("keydown", (e) => {
                console.log("Key pressed:", e.key, e.code, "Ctrl:", e.ctrlKey);
                
                if ((e.ctrlKey || e.metaKey) && e.code === "Space") {
                    e.preventDefault();
                    console.log("Ctrl+Space detected!");
                    const sidebar = Alpine.store("sidebar");
                    console.log("Sidebar store:", sidebar);
                    if (sidebar) {
                        sidebar.isOpen ? sidebar.close() : sidebar.open();
                    }
                }
            });

            // Collapsed за замовчуванням
            document.addEventListener("alpine:init", () => {
                console.log("Alpine init");
                if (!localStorage.getItem("filament-sidebar-initialized")) {
                    localStorage.setItem("filament-sidebar-initialized", "true");
                    setTimeout(() => {
                        Alpine.store("sidebar")?.close();
                        console.log("Sidebar closed by default");
                    }, 100);
                }
            });
            </script>
        ')
        );
    }
}
