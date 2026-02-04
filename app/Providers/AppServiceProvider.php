<?php

namespace App\Providers;

use App\Models\DispatchEntry;
use App\Observers\DispatchEntryObserver;
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
        DispatchEntry::observe(DispatchEntryObserver::class);
    }
}
