<?php

namespace DarkGhostHunter\Larabanker;

use DarkGhostHunter\Transbank\Credentials\Container;
use DarkGhostHunter\Transbank\Events\TransactionCreated;
use DarkGhostHunter\Transbank\Http\Connector;
use DarkGhostHunter\Transbank\Services\OneclickMall;
use DarkGhostHunter\Transbank\Services\Webpay;
use DarkGhostHunter\Transbank\Services\WebpayMall;
use DarkGhostHunter\Transbank\Transbank;
use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Nyholm\Psr7\Factory\Psr17Factory;

class LarabankerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/larabanker.php', 'larabanker');

        $this->app->singleton(
            Transbank::class,
            static function (Application $app): Transbank {
                $transbank = new Transbank(
                    new Container(),
                    $app->make('log'),
                    new EventDispatcher($app->make('events')),
                    new Connector($app->make(Client::class), $factory = new Psr17Factory(), $factory)
                );

                $config = $app->make('config');

                if ($config->get('larabanker.enable', $app->environment('production'))) {
                    $transbank->toProduction($config->get('larabanker.credentials'));
                }

                return $transbank;
            });

        $this->app->singleton(Webpay::class, static function (Application $app): Webpay {
            return $app->make(Transbank::class)->webpay();
        });

        $this->app->singleton(WebpayMall::class, static function (Application $app): WebpayMall {
            return $app->make(Transbank::class)->webpayMall();
        });

        $this->app->singleton(OneclickMall::class, static function (Application $app): OneclickMall {
            return $app->make(Transbank::class)->oneclickMall();
        });

        $this->app->singleton(
            Http\Middleware\EndpointProtect::class,
            static function (Application $app): Http\Middleware\EndpointProtect {
                $config = $app->make('config');

                return new Http\Middleware\EndpointProtect(
                    $config->get('larabanker.protect'),
                    $config->get('larabanker.cache') ?? $config->get('cache.default'),
                    $config->get('larabanker.cache_prefix', 'transbank|token')
                );
            });

        $this->app->bind(
            Listeners\SaveTransactionToken::class,
            static function (Application $app): Listeners\SaveTransactionToken {
                $config = $app->make('config');

                return new Listeners\SaveTransactionToken(
                    $app->make('cache')->store($config->get('larabanker.cache')),
                    $config->get('larabanker.cache_prefix')
                );
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router, Repository $config, Dispatcher $dispatcher): void
    {
        $router->aliasMiddleware('larabanker.protect', Http\Middleware\EndpointProtect::class);

        if ($config->get('larabanker.protect')) {
            $dispatcher->listen(TransactionCreated::class, Listeners\SaveTransactionToken::class);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/larabanker.php' => $this->app->configPath('larabanker.php')
            ], 'config');
        }
    }
}
