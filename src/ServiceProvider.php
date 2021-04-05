<?php

namespace DarkGhostHunter\Larabanker;

use DarkGhostHunter\Larabanker\Http\Middleware\EndpointProtect;
use DarkGhostHunter\Larabanker\Listeners\SaveTransactionToken;
use DarkGhostHunter\Transbank\Credentials\Container;
use DarkGhostHunter\Transbank\Events\TransactionCreated;
use DarkGhostHunter\Transbank\Http\Connector;
use DarkGhostHunter\Transbank\Services\OneclickMall;
use DarkGhostHunter\Transbank\Services\Webpay;
use DarkGhostHunter\Transbank\Services\WebpayMall;
use DarkGhostHunter\Transbank\Transbank;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Nyholm\Psr7\Factory\Psr17Factory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/larabanker.php', 'larabanker'
        );

        $this->app->singleton(Transbank::class, static function(Application $app) : Transbank {
            $transbank = new Transbank(
                new Container(),
                $app->make('log'),
                new EventDispatcher($app->make('events')),
                new Connector($app->make(Client::class), $factory = new Psr17Factory(), $factory)
            );

            $config = $app->make(Repository::class);

            if ($config->get('larabanker.enable') ?? $app->environment('production')) {
                $transbank->toProduction($config->get('larabanker.credentials'));
            }

            return $transbank;
        });

        $this->app->singleton(Webpay::class, static function (Application $app) {
            return $app->make(Transbank::class)->webpay();
        });

        $this->app->singleton(WebpayMall::class, static function (Application $app) {
            return $app->make(Transbank::class)->webpayMall();
        });

        $this->app->singleton(OneclickMall::class, static function (Application $app) {
            return $app->make(Transbank::class)->oneclickMall();
        });

        $this->app->singleton(EndpointProtect::class, static function(Application $app) : EndpointProtect {
            return new EndpointProtect($app->make(Repository::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  \Illuminate\Routing\Router  $router
     * @param  \Illuminate\Config\Repository  $config
     *
     * @return void
     */
    public function boot(Dispatcher $dispatcher, Router $router, Repository $config)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'larabanker');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'larabanker');
        $router->aliasMiddleware('larabanker.protect', EndpointProtect::class);

        if ($config->get('larabanker.protect')) {
            $dispatcher->listen(TransactionCreated::class, SaveTransactionToken::class);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/larabanker.php' => config_path('larabanker.php')], 'config');
            $this->publishes([__DIR__.'/../resources/views' => resource_path('views/vendor/larabanker')], 'views');
            $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang/vendor/larabanker')], 'lang');
        }
    }
}
