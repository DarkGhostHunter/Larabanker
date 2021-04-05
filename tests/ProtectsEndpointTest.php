<?php
/** @noinspection JsonEncodingApiUsageInspection */

namespace Tests;

use DarkGhostHunter\Larabanker\Facades\OneclickMall;
use DarkGhostHunter\Larabanker\Facades\Transbank;
use DarkGhostHunter\Larabanker\Facades\Webpay;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Factory;
use Orchestra\Testbench\TestCase;

class ProtectsEndpointTest extends TestCase
{
    use RegistersPackage;

    public function setUp(): void
    {
        putenv('TRANSBANK_PROTECT=true');

        $this->afterApplicationCreated(
            function () {
                $this->app->make('router')->post(
                    'webpay/return',
                    function () {
                        return 'ok';
                    }
                )->middleware('larabanker.protect');

                $this->app->make('router')->post(
                    'onepay/response',
                    function () {
                        return 'ok';
                    }
                )->middleware('larabanker.protect');
            }
        );

        parent::setUp();
    }

    public function test_doesnt_protect_by_default()
    {
        putenv('TRANSBANK_PROTECT=');

        $this->refreshApplication();

        $this->app->make('router')->post(
            'webpay/return',
            function () {
                return 'ok';
            }
        )->middleware('larabanker.protect');

        $this->post('webpay/return', ['token_ws' => 'test_token'])->assertOk();
    }

    public function test_uses_custom_store()
    {
        config()->set('larabanker.cache', 'file');

        $store = $this->app->make(Factory::class)->store();

        $factory = $this->mock(Factory::class);

        $factory->shouldReceive('store')
            ->with('file')
            ->times(4)
            ->andReturn($store);

        $this->swap(Factory::class, $factory);

        $client = $this->mock(Client::class);

        $client->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200, ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url' => 'test_url'])
                )
            );

        $client->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200, ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url_webpay' => 'test_url'])
                )
            );

        Transbank::webpay()->create('test_buy_order', 1000, 'return_url', 'session_id');

        $this->post('webpay/return', ['token_ws' => 'test_token'])->assertOk();

        Transbank::oneclickMall()->start('test_username', 'foo@bar.com', 'response_url');

        $this->post('webpay/return', ['TBK_TOKEN' => 'test_token'])->assertOk();
    }

    public function test_allows_webpay_transaction_token()
    {
        $client = $this->mock(Client::class);

        $client->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200, ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url' => 'test_url'])
                )
            );

        Transbank::webpay()->create('test_buy_order', 1000, 'return_url', 'session_id');

        $this->post('webpay/return', ['token_ws' => 'test_token'])->assertOk();
    }

    public function test_disallows_webpay_transaction_token_nonexistent()
    {
        $this->mock(Client::class)
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url' => 'test_url'])
                )
            );

        Transbank::webpay()->create('test_buy_order', 1000, 'return_url', 'session_id');

        $this->app->make(Factory::class)->store()->forget('transbank|token|test_token');

        $this->post('webpay/return', ['token_ws' => 'test_token'])->assertNotFound();
    }

    public function test_disallows_webpay_transaction_token_invalid()
    {
        $this->mock(Client::class)
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url' => 'test_url'])
                )
            );

        Transbank::webpay()->create('test_buy_order', 1000, 'return_url', 'session_id');

        $this->post('webpay/return', ['token_ws' => 'invalid'])->assertNotFound();
    }

    public function test_disallows_webpay_transaction_no_token()
    {
        $this->mock(Client::class)
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url' => 'test_url'])
                )
            );

        Transbank::webpay()->create('test_buy_order', 1000, 'return_url', 'session_id');

        $this->post('webpay/return')->assertNotFound();
    }

    public function test_allows_oneclick_transaction_token()
    {
        $client = $this->mock(Client::class);

        $client->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url_webpay' => 'test_url'])
                )
            );

        Transbank::oneclickMall()->start('test_username', 'foo@bar.com', 'response_url');

        $this->post('webpay/return', ['TBK_TOKEN' => 'test_token'])->assertOk();
    }

    public function test_disallows_oneclick_transaction_token_nonexistent()
    {
        $this->mock(Client::class)
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url_webpay' => 'test_url'])
                )
            );

        Transbank::oneclickMall()->start('test_username', 'foo@bar.com', 'response_url');

        $this->app->make(Factory::class)->store()->forget('transbank|token|test_token');

        $this->post('oneclick/response', ['token_ws' => 'test_token'])->assertNotFound();
    }

    public function test_disallows_oneclick_transaction_token_invalid()
    {
        $this->mock(Client::class)
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url_webpay' => 'test_url'])
                )
            );

        Transbank::oneclickMall()->start('test_username', 'foo@bar.com', 'response_url');

        $this->post('oneclick/response', ['token_ws' => 'invalid'])->assertNotFound();
    }

    public function test_disallows_oneclick_transaction_no_token()
    {
        $this->mock(Client::class)
            ->shouldReceive('sendRequest')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(['token' => 'test_token', 'url_webpay' => 'test_url'])
                )
            );

        Transbank::oneclickMall()->start('test_username', 'foo@bar.com', 'response_url');

        $this->post('oneclick/response')->assertNotFound();
    }
}