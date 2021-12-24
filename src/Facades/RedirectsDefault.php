<?php

namespace DarkGhostHunter\Larabanker\Facades;

use function route;

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
        return route(
            static::getFacadeApplication()->make('config')->get("larabanker.redirects.$service")
        );
    }
}