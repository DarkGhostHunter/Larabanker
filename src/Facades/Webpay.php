<?php

namespace DarkGhostHunter\Larabanker\Facades;

use DarkGhostHunter\Transbank\Services\Transactions\Response;
use DarkGhostHunter\Transbank\Services\Webpay as WebpayAccessor;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction commit(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction status(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction refund(string $token, $amount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction capture(string $token, string $buyOrder, int $authorizationCode, $captureAmount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Webpay getFacadeRoot()
 */
class Webpay extends Facade
{
    use RedirectsDefault;
    use RetrievesSessionId;

    /**
     * Creates a ApiRequest on Transbank, returns a response from it.
     *
     * @param  string  $buyOrder
     * @param  int|float  $amount
     * @param  array  $options
     *
     * @return \DarkGhostHunter\Transbank\Services\Transactions\Response
     * @throws \DarkGhostHunter\Transbank\Exceptions\TransbankException
     */
    public static function create(string $buyOrder, $amount, array $options = []): Response
    {
        return static::getFacadeRoot()->create(
            $buyOrder,
            $amount,
            static::redirectFor('webpay'),
            static::generateSessionId(),
            $options,
        );
    }

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return WebpayAccessor::class;
    }
}
