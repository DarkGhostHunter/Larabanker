<?php

namespace Tests\Facades;

use DarkGhostHunter\Larabanker\Facades\OneclickMall;
use DarkGhostHunter\Transbank\Services\OneclickMall as TransbankOneclickMall;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;

class OneclickMallFacadeTest extends TestCase
{
    use RegistersPackage;

    public function test_uses_default_redirection(): void
    {
        $this->mock(TransbankOneclickMall::class)
            ->shouldReceive('start')
            ->with(
                $username = 'test_username',
                $email = 'test_email',
                'http://myapp.com/transbank/oneclickMall/',
                []
            );

        OneclickMall::start($username, $email);
    }

    public function test_uses_default_redirection_and_random_id(): void
    {
        $username = 'test_username';
        $email = 'test_email';
        $responseUrl = 'http://myapp.com/transbank/oneclickMall/';

        $this->mock(TransbankOneclickMall::class)
            ->shouldReceive('start')
            ->withArgs(
                function ($u, $e, $r, $o) use ($responseUrl, $username, $email) {
                    static::assertEquals($username, $u);
                    static::assertEquals($email, $e);
                    static::assertEquals($responseUrl, $r);
                    static::assertEmpty($o);

                    return true;
                }
            );

        OneclickMall::start($username, $email);
    }
}