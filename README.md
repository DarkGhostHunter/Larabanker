![Paul Felberbauer - Unsplash (UL) #-idNOBU5k_80](https://images.unsplash.com/photo-1591030434469-3d78c7b17820?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/larabanker/v/stable)](https://packagist.org/packages/darkghosthunter/larabanker) [![License](https://poser.pugx.org/darkghosthunter/larabanker/license)](https://packagist.org/packages/darkghosthunter/larabanker) ![](https://img.shields.io/packagist/php-v/darkghosthunter/larabanker.svg) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Larabanker/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Larabanker?branch=master) [![Laravel Octane Compatible](https://img.shields.io/badge/Laravel%20Octane-Compatible-success?style=flat&logo=laravel)](https://github.com/laravel/octane)

# Larabanker - Transbank for Laravel

This package connects the non-official [Transbank SDK](https://github.com/DarkGhostHunter/Transbank/) into your Laravel Application.

## Requirements

* PHP >= 8.0
* Laravel 8.x

> Check older releases for older Laravel versions.

## Installation

Call composer and require it into your application.

```bash
composer require darkghosthunter/larabanker
``` 

## Documentation

This package mimics the Transbank SDK, so you should check the documentation of these services in Transbank Developer's site (in spanish).

- [Webpay](https://www.transbankdevelopers.cl/documentacion/webpay-plus#webpay-plus)
- [Webpay Mall](https://www.transbankdevelopers.cl/documentacion/webpay-plus#webpay-plus-mall)
- [Oneclick Mall](https://www.transbankdevelopers.cl/documentacion/oneclick)

## Quickstart

To start using Transbank services, you can use the included `Webpay`, `WebpayMall` and `Oneclick` facades and the `redirect()` method, which use minimum parameters and returns a ready-made `GET` redirect to Transbank. 

```php
use DarkGhostHunter\Larabanker\Facades\Webpay;

return Webpay::redirect('buy-order#1230', 1000);
```

Alternatively, you can still have total control to create transactions using the facades.

```php
use DarkGhostHunter\Larabanker\Facades\Webpay;

$response = Webpay::create('buyOrder#1230', 1000, route('payment'));

return redirect()->away($response, 303);
```

> Since API 1.2, Transbank services support `GET` redirects. There is no longer need to use views with Javascript redirection.

Redirects are made using [default route names](#dealing-with-post-and-session-destruction) that centralizes the payment endpoint.

## Dealing with POST and Session destruction

Laravel sets cookies as `SameSite: lax` by default. This means that the session is destroyed when a payment fails or is aborted. This happens because Transbank redirects using a `POST` method to your application without cookies, forcing Laravel to [recreate a new empty session](https://github.com/laravel/framework/issues/31442).

To avoid this, you should use the same path to receive the response from Transbank, but using a different controller for `GET` or `POST`. Larabanker conveniently uses one route name for each of Transbank services redirection points, which will be hit once the payment process ends.

| Service       | URL          | Name                     | Your hypothetical route                        |
|---------------|--------------|--------------------------|------------------------------------------------|
| Webpay        | Return URL   | `transbank.webpay`       | `http://yourappurl.com/transbank/webpay`       |
| Webpay Mall   | Return URL   | `transbank.webpayMall`   | `http://yourappurl.com/transbank/webpayMall`   |
| Oneclick Mall | Response URL | `transbank.oneclickMall` | `http://yourappurl.com/transbank/oneclickMall` |

You're free to [change these Route names in the config file](#redirection). Be sure to add your controllers for these routes to process the incoming response from Transbank.

In this example, we will disable the `web` middleware to avoid creating a new session, and return a view with a generic failure message.

```php
use \DarkGhostHunter\Larabanker\Facades\Webpay;
use \Illuminate\Support\Facades\Route;

Route::get('transbank/webpay', function (Request $request) {
    $transaction = Webpay::commit($request->input('token_ws'));

    return view('payment.processed')->with('transaction', $transaction);
})->name('transbank.webpay');

Route::post('transaction/webpay, function (Request $request) {
    return view('payment.failed');
})->withoutMiddleware('web');
```

## Configuration

While Larabanker is made to use conveniently without setting too much, you can go deeper by publishing the configuration file:

    php artisan vendor:publish --provider="DarkGhostHunter\Larabanker\ServiceProvider"

You will receive the `larabanker.php` config file with the following contents:

```php
<?php

return [
    'environment' => env('TRANSBANK_ENV', 'integration'),
    'credentials' => [
        // ...
    ],
    'redirects' => [
        'webpay'       => 'transbank.webpay',
        'webpayMall'   => 'transbank.webpayMall',
        'oneclickMall' => 'transbank.oneclickMall',
    ],
    'protect' => env('TRANSBANK_PROTECT', false),
    'cache' => null,
    'cache_prefix' => env('TRANSBANK_PROTECT_PREFIX', 'transbank|token')
];
```

Don't worry, we will explain each important part one by one.

### Environment & Credentials

```php
<?php

return [
    'environment' => env('TRANSBANK_ENV', 'integration'),
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
];
```

By default, the package uses the `integration` environment, which makes fake transactions.

To use the `production` environment, which will make all transactions real, set the environment as `production` **explicitly**:

```dotenv
TRANSBANK_ENV=production
```

Additionally, you must add your Transbank credentials for your services, which will be issued directly to you, for the service(s) you have contracted. You can do it easily in your `.env` file.

```dotenv
WEBPAY_KEY=5000000001
WEBPAY_SECRET=dKVhq1WGt_XapIYirTXNyUKoWTDFfxaEV63-O5jcsdw
```

### Redirection

```php
<?php

return [
    'redirects_base' => env('APP_URL'),
    'redirects' => [
        'webpay'       => 'transbank.webpay',
        'webpayMall'   => 'transbank.webpayMall',
        'oneclickMall' => 'transbank.oneclickMall',
    ],
];
```

Only when using the `Webpay`, `WebpayMall` and `OneclickMall` facades, you will be able to skip issuing the `$returnUrl` or `$responseUrl` values to the transaction creation, letting Larabanker to use the defaults issued in your config file.

```php
use DarkGhostHunter\Larabanker\Facades\Webpay;

$response = Webpay::create('myOrder#16548', 1000);
```

### Endpoint Protection

```php
return [
    'protect' => env('TRANSBANK_PROTECT', false),
    'cache' => null,
    'cache_prefix' => env('TRANSBANK_PROTECT_PREFIX', 'transbank|token')
];
```

Disabled by default, this package offers a brute-force attack protection middleware, `larabank.protect`, for return URL. These return URLs are your application endpoints that Transbank services will redirect the user to using a `GET` or `POST` request.

If it's disabled, the middleware won't verify the token. 

```php
use \Illuminate\Support\Facades\Route;
use \App\Http\Controllers\WebpayController;

Route::post('/transbank/webpay', [WebpayController::class, 'receivePayment'])
     ->middleware('larabank.protect');
```

It uses the cache to save the transaction token for 5 minutes, so if the token is no longer valid, the whole response is aborted. You can change the cache store and prefix with `cache` and `cache_prefix`, respectively.

> This works for receiving the redirection from Transbank on Webpay, Webpay Mall and Oneclick Mall services.

## License

This package is open-sourced software licensed under the MIT license.

`Redcompra`, `Webpay`, `Onepay`, `Patpass` and `tbk` are trademarks of [Transbank S.A.](https://www.transbank.cl/)

This package is not developed, approved, supported nor endorsed by Transbank S.A., nor by a natural person or company linked directly or indirectly by Transbank S.A.
