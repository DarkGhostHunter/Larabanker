<?php

namespace Tests\Facades;

use DarkGhostHunter\Larabanker\Facades\Webpay;
use DarkGhostHunter\Transbank\Services\Webpay as TransbankWebpay;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;

class WebpayFacadeTest extends TestCase
{
    use RegistersPackage;

    public function test_uses_default_redirection_and_session_id(): void
    {
        session()->start();

        $id = session()->getId();

        $this->mock(TransbankWebpay::class)
            ->shouldReceive('create')
            ->with(
                $buyOrder = 'test_buy_order',
                $amount = 1000,
                'http://myapp.com/transbank/webpay/',
                $id,
                []
            );

        Webpay::create($buyOrder, $amount);
    }

    public function test_uses_default_redirection_and_random_id(): void
    {
        static::assertFalse(session()->isStarted());

        $buyOrder = 'test_buy_order';
        $amount = 1000;
        $returnUrl = 'http://myapp.com/transbank/webpay/';

        $this->mock(TransbankWebpay::class)
            ->shouldReceive('create')
            ->withArgs(function ($b, $a, $r, $s, $o) use ($returnUrl, $amount, $buyOrder) {
                static::assertEquals($buyOrder, $b);
                static::assertEquals($amount, $a);
                static::assertEquals($returnUrl, $r);
                static::assertEquals(20, strlen($s));
                static::assertEmpty($o);

                return true;
            });

        Webpay::create($buyOrder, $amount);
    }
}