<?php

namespace Tests\Facades;

use DarkGhostHunter\Larabanker\Facades\Webpay;
use DarkGhostHunter\Transbank\Services\Webpay as TransbankWebpay;
use Orchestra\Testbench\TestCase;
use Tests\DefaultRoutes;
use Tests\RegistersPackage;
use function strlen;

class WebpayFacadeTest extends TestCase
{
    use RegistersPackage;
    use DefaultRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultsRoutes();
    }

    public function test_uses_default_redirection_and_random_session_id(): void
    {
        $buyOrder = 'test_buy_order';
        $amount = 1000;

        $this->mock(TransbankWebpay::class)
            ->allows('create')
            ->withArgs(static function ($buyOrder, $amount, $url, $id, $options): bool {
                static::assertSame($buyOrder, 'test_buy_order');
                static::assertSame($amount, 1000);
                static::assertSame($url, 'http://myapp.com/transbank/webpay');
                static::assertSame(strlen($id), 10);
                static::assertSame($options, []);

                return true;
            });

        Webpay::redirect($buyOrder, $amount);
    }
}