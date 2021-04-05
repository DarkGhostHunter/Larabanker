<?php

namespace DarkGhostHunter\Larabanker\Facades;

use DarkGhostHunter\Transbank\Transbank as TransbankAccessor;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isIntegration()
 * @method static bool isProduction()
 * @method static \DarkGhostHunter\Transbank\Services\Webpay webpay()
 * @method static \DarkGhostHunter\Transbank\Services\WebpayMall webpayMall()
 * @method static \DarkGhostHunter\Transbank\Services\OneclickMall oneclickMall()
 * @method static \DarkGhostHunter\Transbank\Transbank getFacadeRoot()
 */
class Transbank extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return TransbankAccessor::class;
    }
}
