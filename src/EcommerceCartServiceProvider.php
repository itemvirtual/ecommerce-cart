<?php

namespace Itemvirtual\EcommerceCart;

use Illuminate\Support\ServiceProvider;

class EcommerceCartServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ecommerce-cart');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'ecommerce-cart');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('ecommerce-cart.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/ecommerce-cart'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/ecommerce-cart'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/ecommerce-cart'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'ecommerce-cart');

        // Add ecommerce-cart log channel to project
        $this->mergeConfigFrom(__DIR__ . '/../config/log-channel.php', 'logging.channels');

        // Register the main class to use with the facade
        $this->app->singleton('ecommerce-cart', function () {
            return new EcommerceCart;
        });
    }
}
