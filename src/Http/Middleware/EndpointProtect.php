<?php

namespace DarkGhostHunter\Larabanker\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EndpointProtect
{
    /**
     * CacheTransactionToken constructor.
     *
     * @param  bool  $enabled
     * @param  string  $store
     * @param  string  $prefix
     */
    public function __construct(protected bool $enabled, protected string $store, protected string $prefix)
    {
        //
    }

    /**
     * Handle the incoming Transbank POST Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return void
     */
    public function handle(Request $request, Closure $next): mixed
    {
        abort_if($this->enabled && !$this->check($request), 404);

        return $next($request);
    }

    /**
     * Checks if the token was issued. If it is, it removes it from the cache.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function check(Request $request): bool
    {
        $token = $request->input('token_ws') ?? $request->input('TBK_TOKEN');

        return $token && app('cache')->store($this->store)->pull(Str::finish($this->prefix, '|') . $token);
    }
}