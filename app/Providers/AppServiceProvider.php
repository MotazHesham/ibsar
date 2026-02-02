<?php

namespace App\Providers;

use App\Helpers\ThemeHelper;
use App\Models\Consultant;
use App\Observers\ConsultantObserver;
use Illuminate\Support\ServiceProvider; 
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{ 

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive(); // use the bootstrap 5 for pagination

        Consultant::observe(ConsultantObserver::class);

        view()->composer('*', function ($view) {
            $view->with('themeSettings', ThemeHelper::applyThemeSettings());
            $view->with('themeCheckedStates', ThemeHelper::getCheckedStates());
        });
    }
}
