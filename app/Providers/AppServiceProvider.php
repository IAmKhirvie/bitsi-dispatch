<?php

namespace App\Providers;

use App\Http\Livewire\UpdateProfileInformationForm;
use App\Models\DispatchEntry;
use App\Observers\DispatchEntryObserver;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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

        // Override Jetstream's UpdateProfileInformationForm
        Livewire::component('profile.update-profile-information-form', UpdateProfileInformationForm::class);
    }
}
