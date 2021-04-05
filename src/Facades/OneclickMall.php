<?php

namespace DarkGhostHunter\Larabanker\Facades;

use DarkGhostHunter\Transbank\Services\OneclickMall as OneclickMallAccessor;
use DarkGhostHunter\Transbank\Services\Transactions\Response;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction finish(string $token, array $options = [])
 * @method static void delete(string $tbkUser, string $username, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction authorize(string $tbkUser, string $username, string $buyOrder, array $details, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction status(string $buyOrder, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction refund(string $buyOrder, string $childCommerceCode, string $childBuyOrder, $amount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Transaction capture(string $commerceCode, string $buyOrder, $authorizationCode, $captureAmount, array $options = [])
 * @method static \DarkGhostHunter\Transbank\Services\OneclickMall getFacadeRoot()
 */
class OneclickMall extends Facade
{
    use RedirectsDefault;

    /**
     * Creates a new pending subscription in Transbank.
     *
     * @param  string  $username
     * @param  string  $email
     * @param  array  $options
     *
     * @return \DarkGhostHunter\Transbank\Services\Transactions\Response
     * @throws \DarkGhostHunter\Transbank\Exceptions\TransbankException
     */
    public static function start(string $username, string $email, array $options = []): Response
    {
        return static::getFacadeRoot()->start($username, $email, static::redirectFor('oneclickMall'), $options);
    }

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return OneclickMallAccessor::class;
    }
}