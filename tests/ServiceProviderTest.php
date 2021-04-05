<?php

namespace Tests;

use DarkGhostHunter\Larabanker\Http\Middleware\EndpointProtect;
use DarkGhostHunter\Transbank\Events\TransactionCreated;
use DarkGhostHunter\Transbank\Services\OneclickMall;
use DarkGhostHunter\Transbank\Services\Webpay;
use DarkGhostHunter\Transbank\Services\WebpayMall;
use DarkGhostHunter\Transbank\Transbank;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    use RegistersPackage;

    public function test_publishes_config(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'DarkGhostHunter\Larabanker\ServiceProvider',
            '--tag' => 'config'
        ])->execute();

        static::assertFileEquals(__DIR__ . '/../config/larabanker.php', config_path('larabanker.php'));
    }

    public function test_publishes_views(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'DarkGhostHunter\Larabanker\ServiceProvider',
            '--tag' => 'views'
        ])->execute();

        static::assertFileEquals(
            __DIR__ . '/../resources/views/webpay/redirect.blade.php',
            resource_path('views/vendor/larabanker/webpay/redirect.blade.php')
        );

        static::assertFileEquals(
            __DIR__ . '/../resources/views/oneclick/redirect.blade.php',
            resource_path('views/vendor/larabanker/oneclick/redirect.blade.php')
        );
    }

    public function test_publishes_translations(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'DarkGhostHunter\Larabanker\ServiceProvider',
            '--tag' => 'lang'
        ])->execute();

        static::assertFileEquals(
            __DIR__ . '/../resources/lang/en/redirect.php',
            resource_path('lang/vendor/larabanker/en/redirect.php')
        );

        static::assertFileEquals(
            __DIR__ . '/../resources/lang/es/redirect.php',
            resource_path('lang/vendor/larabanker/es/redirect.php')
        );
    }

    public function test_registers_middleware(): void
    {
        static::assertArrayHasKey('larabanker.protect', $this->app->make('router')->getMiddleware());
    }

    public function test_registers_facades(): void
    {
        static::assertInstanceOf(Transbank::class, \Transbank::getFacadeRoot());
        static::assertInstanceOf(Webpay::class, \Webpay::getFacadeRoot());
        static::assertInstanceOf(WebpayMall::class, \WebpayMall::getFacadeRoot());
        static::assertInstanceOf(OneclickMall::class, \OneclickMall::getFacadeRoot());
    }

    public function test_registers_endpoint_protection(): void
    {
        static::assertTrue($this->app->has(EndpointProtect::class));
    }

    public function test_registers_listener_if_protected()
    {
        putenv('TRANSBANK_PROTECT=true');

        $this->refreshApplication();

        static::assertTrue($this->app->make('events')->hasListeners(TransactionCreated::class));
    }

    public function test_registers_listener_if_not_protected()
    {
        putenv('TRANSBANK_PROTECT=false');

        $this->refreshApplication();

        static::assertFalse($this->app->make('events')->hasListeners(TransactionCreated::class));

        putenv('TRANSBANK_PROTECT=');

        $this->refreshApplication();

        static::assertFalse($this->app->make('events')->hasListeners(TransactionCreated::class));
    }

    protected function tearDown() : void
    {
        @unlink(config_path('larabanker.php'));
        @unlink(resource_path('views/vendor/larabanker/oneclick/redirect.blade.php'));
        @unlink(resource_path('views/vendor/larabanker/webpay/redirect.blade.php'));
        @unlink(resource_path('lang/vendor/larabanker/es/redirect.php'));
        @unlink(resource_path('lang/vendor/larabanker/en/redirect.php'));

        putenv('TRANSBANK_PROTECT=');

        parent::tearDown();
    }
}