<?php

namespace DarkGhostHunter\Larabanker\Facades;

use Illuminate\Support\Str;

trait RedirectsDefault
{
    /**
     * Returns the redirection URL for the given service.
     *
     * @param  string  $service
     *
     * @return string
     */
    protected static function redirectFor(string $service): string
    {
        $config = static::getFacadeApplication()->make('config');

        return Str::finish($config->get('larabanker.redirects_base'), '/') .
               ltrim($config->get("larabanker.redirects.$service"), '/') . '/';
    }
}