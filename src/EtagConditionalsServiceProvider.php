<?php

namespace Werk365\EtagConditionals;

use Illuminate\Support\ServiceProvider;
use Werk365\EtagConditionals\Middleware\IfMatch;
use Werk365\EtagConditionals\Middleware\IfNoneMatch;
use Werk365\EtagConditionals\Middleware\SetEtag;

class EtagConditionalsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        //Middlewares
        $middlewares = [
            SetEtag::class,
            IfNoneMatch::class,
            IfMatch::class,
        ];

        // Set middleware group
        $this->app['router']->middlewareGroup('etag', $middlewares);

        // Set individual middlewares
        foreach ($middlewares as $middleware) {
            $this->app['router']->aliasMiddleware((new $middleware)->name(), $middleware);
        }

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/etagconditionals.php', 'etagconditionals');

        // Register the service the package provides.
        $this->app->singleton('etagconditionals', function ($app) {
            return new EtagConditionals;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['etagconditionals'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/etagconditionals.php' => config_path('etagconditionals.php'),
        ], 'etagconditionals.config');
    }
}
