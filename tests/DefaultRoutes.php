<?php

namespace Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait DefaultRoutes
{
    protected function defaultsRoutes(): void
    {
        Route::match(['get', 'post'], 'transbank/webpay', static function (Request $request): string {
            return $request->input('token_ws');
        })->name('transbank.webpay');

        Route::match(['get', 'post'], 'transbank/oneclickMall', static function (Request $request): string {
            return $request->input('token_ws');
        })->name('transbank.oneclickMall');

        Route::match(['get', 'post'], 'transbank/webpayMall', static function (Request $request): string {
            return $request->input('token_ws');
        })->name('transbank.webpayMall');
    }
}