<?php

namespace Tests\Facades;

use DarkGhostHunter\Larabanker\Facades\WebpayMall;
use DarkGhostHunter\Transbank\Services\WebpayMall as TransbankWebpayMall;
use Orchestra\Testbench\TestCase;
use Tests\DefaultRoutes;
use Tests\RegistersPackage;

class WebpayMallFacadeTest extends TestCase
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
        $details = [['foo' => 'bar'], ['baz' => 'qux']];
        $returnUrl = 'http://myapp.com/transbank/webpayMall';

        $this->mock(TransbankWebpayMall::class)
            ->allows('create')
            ->withArgs(function ($b, $r, $s, $d, $o) use ($returnUrl, $details, $buyOrder) {
                static::assertEquals($buyOrder, $b);
                static::assertEquals($details, $d);
                static::assertEquals($returnUrl, $r);
                static::assertEquals(10, strlen($s));
                static::assertEmpty($o);

                return true;
            });

        WebpayMall::redirect($buyOrder, $details);
    }
}