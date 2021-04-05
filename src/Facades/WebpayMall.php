<?php

namespace DarkGhostHunter\Larabanker\Facades;

use DarkGhostHunter\Transbank\Services\Transactions\Response;
use DarkGhostHunter\Transbank\Services\WebpayMall as WebpayMallAccessor;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction commit(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction status(string $token, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction refund($commerceCode, string $token, string $buyOrder, $amount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction capture($commerceCode, string $token, string $buyOrder, $authorizationCode, $captureAmount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\WebpayMall getFacadeRoot()
 */
class WebpayMall extends Facade
{
    use RedirectsDefault;
    use RetrievesSessionId;

    /**
     * Creates a Webpay Mall transaction.
     *
     * @param  string  $buyOrder
     * @param  array  $details
     * @param  array  $options
     *
     * @return \DarkGhostHunter\Transbank\Services\Transactions\Response
     * @throws \DarkGhostHunter\Transbank\Exceptions\TransbankException
     */
    public static function create(string $buyOrder, array $details, array $options = []): Response
    {
        return static::getFacadeRoot()->create(
            $buyOrder,
            static::redirectFor('webpayMall'),
            static::generateSessionId(),
            $details,
            $options,
        );
    }

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return WebpayMallAccessor::class;
    }
}