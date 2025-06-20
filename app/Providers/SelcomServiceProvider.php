<?php

namespace App\Providers;

use App\Services\PaymentService\SelcomService;
use Illuminate\Support\ServiceProvider;

class SelcomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SelcomService::class, function ($app) {
            return new SelcomService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../../config/selcom.php' => config_path('selcom.php'),
        ], 'config');
    }
}
