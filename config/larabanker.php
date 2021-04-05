<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | By default, the environment in your application will be 'integration'.
    | When you're ready to accept real payments using Transbank services,
    | change the environment to 'production' to use your credentials.
    |
    | Supported: 'integration', 'production'
    |
    */

    'environment' => env('TRANSBANK_ENV', 'integration'),

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | Here you put each of the credentials for each service you use. These are
    | not required if you're using this SDK with fake transactions, but once
    | you set the `production` environment these will be mandatory to use.
    |
    */

    'credentials' => [
        'webpay' => [
            'key' => env('WEBPAY_KEY'),
            'secret' => env('WEBPAY_SECRET'),
        ],
        'webpayMall' => [
            'key' => env('WEBPAY_MALL_KEY'),
            'secret' => env('WEBPAY_MALL_SECRET'),
        ],
        'oneclickMall' => [
            'key' => env('ONECLICK_MALL_KEY'),
            'secret' => env('ONECLICK_MALL_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults redirects
    |--------------------------------------------------------------------------
    |
    | Once you create a transaction in Transbank and the user completes it the
    | servers will redirect the user browser to your site. These URLs will be
    | injected when the user is redirected when you use each service Facade.
    |
    */

    'redirects_base' => env('APP_URL'),

    'redirects' => [
        'webpay'       => '/transbank/webpay',
        'webpayMall'   => '/transbank/webpayMall',
        'oneclickMall' => '/transbank/oneclickMall',
    ],


    /*
    |--------------------------------------------------------------------------
    | Brute-force protection
    |--------------------------------------------------------------------------
    |
    | To avoid brute-force attacks to your Transbank endpoints, you can use the
    | protection on these endpoints using the `larabanker.protect` middleware.
    | To enable it, set the `protect` key or the `TRANSBANK_PROTECT` to true.
    |
    */

    'protect' => env('TRANSBANK_PROTECT', false),
    'cache' => null,
    'cache_prefix' => env('TRANSBANK_PROTECT_PREFIX', 'transbank|token'),
];
