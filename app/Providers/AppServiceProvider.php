<?php

namespace App\Providers;

use App\Http\Helpers\InfusionsoftHelper;
use App\Services\Reminder;
use App\Services\ReminderService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Reminder::class, function () {
            return new ReminderService();
        });

        $this->app->singleton(InfusionsoftHelper::class, function () {
            return new InfusionsoftHelper();
        });
    }
}
