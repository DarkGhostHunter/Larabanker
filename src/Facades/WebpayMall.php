<?php

namespace DarkGhostHunter\Larabanker\Facades;

use DarkGhostHunter\Transbank\Services\Transactions\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Response create(string $buyOrder, string $returnUrl, string $sessionId, array $details, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction commit(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction status(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction refund($commerceCode, string $token, string $buyOrder, $amount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction capture($commerceCode, string $token, string $buyOrder, $authorizationCode, $captureAmount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\WebpayMall getFacadeRoot()
 */
class WebpayMall extends Facade
{
    use RedirectsDefault;

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return \DarkGhostHunter\Transbank\Services\WebpayMall::class;
    }

    /**
     * Creates a Webpay Mall transaction.
     *
     * @param  string  $buyOrder
     * @param  array  $details
     * @param  array  $options
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirect(string $buyOrder, array $details, array $options = []): RedirectResponse
    {
        return Redirect::away(
            static::create($buyOrder, static::redirectFor('webpayMall'), Str::random(10), $details, $options)
        );
    }
}