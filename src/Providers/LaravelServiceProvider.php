<?php

namespace AhmedEbead\Moyasar\Providers;

use AhmedEbead\Moyasar\Moyasar;
use AhmedEbead\Moyasar\Services\InvoiceService;
use AhmedEbead\Moyasar\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('moyasar-http-client', function ($app) {
            return (new GuzzleClientFactory)->build();
        });

        $this->app->bind(\AhmedEbead\Moyasar\Contracts\HttpClient::class, function ($app) {
            return new HttpClient($app->make('moyasar-http-client'));
        });

        $this->app->bind(PaymentService::class, function ($app) {
            return new PaymentService($app->make(\AhmedEbead\Moyasar\Contracts\HttpClient::class));
        });

        $this->app->bind(InvoiceService::class, function ($app) {
            return new InvoiceService($app->make(\AhmedEbead\Moyasar\Contracts\HttpClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../../config/config.php');
        $this->publishes([$path => config_path('moyasar.php')], 'config');
        $this->mergeConfigFrom($path, 'moyasar');
        $config = $this->app->make('config');
    }
}
