<?php

namespace App\Providers;

use App\Models\AgendaItem;
use App\Models\PickupMoment;
use App\Observers\AgendaItemObserver;
use App\Observers\PickupMomentObserver;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        // Register observers
        AgendaItem::observe(AgendaItemObserver::class);
        PickupMoment::observe(PickupMomentObserver::class);
    }
}
