<?php

namespace Tests;

use DarkGhostHunter\Larabanker\Facades\OneclickMall;
use DarkGhostHunter\Larabanker\Facades\Transbank;
use DarkGhostHunter\Larabanker\Facades\Webpay;
use DarkGhostHunter\Larabanker\Facades\WebpayMall;
use DarkGhostHunter\Larabanker\ServiceProvider;

trait RegistersPackage
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Transbank' => Transbank::class,
            'Webpay' => Webpay::class,
            'WebpayMall' => WebpayMall::class,
            'OneclickMall' => OneclickMall::class,
        ];
    }
}