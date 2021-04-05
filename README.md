![Sharon McCutcheon - Unsplash (UL) #-8a5eJ1-mmQ](https://images.unsplash.com/photo-1518458028785-8fbcd101ebb9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/larabanker/v/stable)](https://packagist.org/packages/darkghosthunter/larabanker) [![License](https://poser.pugx.org/darkghosthunter/larabanker/license)](https://packagist.org/packages/darkghosthunter/larabanker)
![](https://img.shields.io/packagist/php-v/darkghosthunter/larabanker.svg)
[![Build Status](https://travis-ci.com/darkghosthunter/larabanker.svg?branch=master)](https://travis-ci.com/darkghosthunter/larabanker) [![Coverage Status](https://coveralls.io/repos/github/darkghosthunter/larabanker/badge.svg?branch=master)](https://coveralls.io/github/darkghosthunter/larabanker?branch=master)

# Larabanker - Transbank for Laravel

This package connects the non-official [Transbank SDK](https://github.com/DarkGhostHunter/Transbank/) into your Laravel Application.

## Requirements

* PHP >= 7.3
* Laravel 7.x or 8.x

> Check older releases for older Laravel versions.

## Installation

Call composer and require it into your application.

```bash
composer require darkghosthunter/larabanker
``` 

## Documentation

This package mimics the Transbank SDK, so you should check the documentation of these services in Transbank Developer's site (in spanish).

- [Webpay](https://www.transbankdevelopers.cl/documentacion/webpay-plus#webpay-plus) - [[English translated]](https://translate.google.com/translate?hl=&sl=es&tl=en&u=https%3A%2F%2Fwww.transbankdevelopers.cl%2Fdocumentacion%2Fwebpay-plus)
- [Webpay Mall](https://www.transbankdevelopers.cl/documentacion/webpay-plus#webpay-plus-mall) - [[English translated]](https://translate.google.com/translate?hl=&sl=es&tl=en&u=https%3A%2F%2Fwww.transbankdevelopers.cl%2Fdocumentacion%2Fwebpay-plus%23webpay-plus-mall)
- [Oneclick Mall](https://www.transbankdevelopers.cl/documentacion/oneclick) - [[English translated]](https://translate.google.com/translate?hl=&sl=es&tl=en&u=https%3A%2F%2Fwww.transbankdevelopers.cl%2Fdocumentacion%2Foneclick)

## Quickstart

To start playing with Transbank services, you can use the included `Webpay`, `WebpayMall` and `Oneclick` facades which use minimum parameters.

Along the facades, you can also use the `larabanker::webpay.redirect` or `larabanker::oneclick.redirect` views to redirect the user to Transbank and complete the Webpay payment or Oneclick registration, respectively.

```php
use DarkGhostHunter\Larabanker\Facades\Webpay;

$response = Webpay::create('buy-order#1230', 1000);

return view('larabanker::webpay.redirect')->with('response', $response);
```

The redirection URLs, which Transbank uses to redirect the user back to your application once the payment process is complete, are these by default. 

| Service | URL | Value |
|---|---|---|
| Webpay        | Return URL        | `http://yourappurl.com/transbank/webpay`
| Webpay Mall   | Return URL        | `http://yourappurl.com/transbank/webpayMall`
| Oneclick Mall | Response URL      | `http://yourappurl.com/transbank/oneclickMall`

You're free to [change these URLs with the config file](#redirection). Be sure to add your controllers for these routes to process the incoming response.

```php
<?php

use \Illuminate\Support\Facades\Route;
use \App\Http\Controllers\WebpayController;

Route::post('/transbank/webpay', [WebpayController::class, 'receivePayment']);
```

```php
<?php

namespace App\Http\Controllers;

use DarkGhostHunter\Larabanker\Facades\Webpay;

class WebpayController extends Controller
{
    /**
     * Process the payment. 
     * 
     * @param  \App\Http\Controllers\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function receivePayment(Request $request)
    {
        $transaction = Webpay::commit($request->input('token_ws'));
        
        return view('payment.processed')->with('transaction', $transaction);
    }
}
```

In any case, be sure to add your logic in these routes to receive Transbank http POST Requests, and **remove the `csrf` middleware** since Transbank will need to hit these routes to complete the transaction.

```php
<?php 

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'transbank/*', // If you're using the default routes.
    ];
}
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
    'redirects_base' => env('APP_URL'),
    'redirects' => [
        'webpay'       => '/transbank/webpay',
        'webpayMall'   => '/transbank/webpayMall',
        'oneclickMall' => '/transbank/oneclickMall',
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
        'webpay'       => '/transbank/webpay',
        'webpayMall'   => '/transbank/webpayMall',
        'oneclickMall' => '/transbank/oneclickMall',
    ],
];
```

Only using the `Webpay`, `WebpayMall` and `OneclickMall` facades, you will be able to skip issuing the `$returnUrl` or `$responseUrl` values to the transaction creation, letting Larabanker to use the defaults issued in your config file.

```php
use DarkGhostHunter\Larabanker\Facades\Webpay;

$response = Webpay::create('myOrder#16548', 1000);
```

Additionally, it will also push the Session ID to the transaction, so you can retrieve it and continue the session in another device if you want. If the Session has not started, or is unavailable like on stateless routes, a throwaway random Session ID will be generated.

> If you need control over the parameters, you can use the `Transbank` Facade directly and call the service method.
> 
> ```php
> use DarkGhostHunter\Larabanker\Facades\Transbank;
> 
> $response = Transbank::webpay()->create('myOrder#16548', 1000, 'https://app.com/return', 'my-sessionId');
> ```

### Endpoint Protection

```php
<?php

return [
    'protect' => env('TRANSBANK_PROTECT', false),
    'cache' => null,
    'cache_prefix' => env('TRANSBANK_PROTECT_PREFIX', 'transbank|token')
];
```

Disabled by default, this package offers a brute-force attack protection middleware, `larabank.protect`, for return URL. These return URLs are your application endpoints that Transbank services will redirect the user to, using a `POST` request.

It works transparently, so there if it's disabled, the middleware won't verify the token. 

```php
use \Illuminate\Support\Facades\Route;
use \App\Http\Controllers\WebpayController;

Route::post('/transbank/webpay', [WebpayController::class, 'receivePayment'])
     ->middleware('larabank.protect');
```

It uses the cache to save the transaction token for 5 minutes, so if the token is not valid, the whole response is aborted. You can change the cache store and prefix with `cache` and `cache_prefix`, respectively.

> This works for receiving the redirection from Transbank on Webpay, Webpay Mall and Oneclick Mall services.

## License

This package is open-sourced software licensed under the MIT license.

`Redcompra`, `Webpay`, `Onepay`, `Patpass` and `tbk` are trademarks of [Transbank S.A.](https://www.transbank.cl/)

This package is not developed, approved, supported nor endorsed by Transbank S.A., nor by a natural person or company linked directly or indirectly by Transbank S.A.
