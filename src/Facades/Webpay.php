<?php

namespace DarkGhostHunter\Larabanker\Facades;

use DarkGhostHunter\Transbank\Services\Transactions\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Response create(string $buyOrder, int|float $amount, string $returnUrl, ?string $sessionId, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction commit(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction status(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction refund(string $token, $amount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction capture(string $token, string $buyOrder, int $authorizationCode, $captureAmount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Webpay getFacadeRoot()
 */
class Webpay extends Facade
{
    use RedirectsDefault;

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return \DarkGhostHunter\Transbank\Services\Webpay::class;
    }

    /**
     * Creates a redirection to Transbank.
     *
     * @param  string  $buyOrder
     * @param  int|float  $amount
     * @param  array  $options
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirect(string $buyOrder, int|float $amount, array $options = []): RedirectResponse
    {
        return Redirect::away(
            static::create($buyOrder, $amount, static::redirectFor('webpay'), Str::random(10), $options), 303
        );
    }
}
