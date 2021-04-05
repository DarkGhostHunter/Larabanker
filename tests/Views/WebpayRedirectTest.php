<?php

namespace Tests\Views;

use DarkGhostHunter\Transbank\Services\Transactions\Response;
use Illuminate\Support\Facades\Lang;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;

class WebpayRedirectTest extends TestCase
{
    use RegistersPackage;

    public function test_webpay_accepts_url_and_token(): void
    {
        $render = view('larabanker::webpay.redirect')->with('response', new Response('test_token', 'test_url'));

        static::assertStringContainsString(
            '<form id="redirect" action="test_url" method="post">',
            $render
        );

        static::assertStringContainsString(
            '<input type="hidden" name="token_ws" value="test_token">',
            $render
        );
    }

    public function test_webpay_accepts_title_translation(): void
    {
        Lang::setLocale('en');
        $render = view('larabanker::webpay.redirect')->with('response', new Response('test_token', 'test_url'));

        static::assertStringContainsString('<title>Connecting to Transbank...</title>', $render);

        Lang::setLocale('es');
        $render = view('larabanker::webpay.redirect')->with('response', new Response('test_token', 'test_url'));

        static::assertStringContainsString('<title>Conectando con Transbank...</title>', $render);
    }
}