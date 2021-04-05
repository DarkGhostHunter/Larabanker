<?php

namespace Tests\Facades;

use DarkGhostHunter\Larabanker\Facades\WebpayMall;
use DarkGhostHunter\Transbank\Services\WebpayMall as TransbankWebpayMall;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;

class WebpayMallFacadeTest extends TestCase
{
    use RegistersPackage;

    public function test_uses_default_redirection_and_session_id(): void
    {
        session()->start();

        $id = session()->getId();

        $this->mock(TransbankWebpayMall::class)
            ->shouldReceive('create')
            ->with(
                $buyOrder = 'test_buy_order',
                'http://myapp.com/transbank/webpayMall/',
                $id,
                $details = [['foo' => 'bar'], ['baz' => 'qux']],
                []
            );

        WebpayMall::create($buyOrder, $details);
    }

    public function test_uses_default_redirection_and_random_id(): void
    {
        static::assertFalse(session()->isStarted());

        $buyOrder = 'test_buy_order';
        $details = [['foo' => 'bar'], ['baz' => 'qux']];
        $returnUrl = 'http://myapp.com/transbank/webpayMall/';

        $this->mock(TransbankWebpayMall::class)
            ->shouldReceive('create')
            ->withArgs(function ($b, $r, $s, $d, $o) use ($returnUrl, $details, $buyOrder) {
                static::assertEquals($buyOrder, $b);
                static::assertEquals($details, $d);
                static::assertEquals($returnUrl, $r);
                static::assertEquals(20, strlen($s));
                static::assertEmpty($o);

                return true;
            });

        WebpayMall::create($buyOrder, $details);
    }
}