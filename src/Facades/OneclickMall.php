<?php

namespace DarkGhostHunter\Larabanker\Facades;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Redirect;

/**
 * @method static \DarkGhostHunter\Transbank\Services\Transactions\Response start(string $username, string $email, string $responseUrl, array $options = [])
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
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return \DarkGhostHunter\Transbank\Services\OneclickMall::class;
    }

    /**
     * Creates a redirection to Transbank.
     *
     * @param  string  $username
     * @param  string  $email
     * @param  array  $options
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirect(string $username, string $email, array $options = []): RedirectResponse
    {
        return Redirect::away(static::start($username, $email, static::redirectFor('oneclickMall'), $options));
    }
}