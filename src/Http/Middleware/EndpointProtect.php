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
     * Cache repository.
     *
     * @var string
     */
    protected $store;

    /**
     * Prefix for the cache key.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Check if the protection should be enabled
     *
     * @var bool
     */
    protected $enabled;

    /**
     * CacheTransactionToken constructor.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     */
    public function __construct(Repository $config)
    {
        $this->enabled = $config->get('larabanker.protect');
        $this->store = $config->get('larabanker.cache');
        $this->prefix = Str::finish($config->get('larabanker.cache_prefix', 'transbank|token'), '|');
    }

    /**
     * Handle the incoming Transbank POST Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return void|mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->enabled || $this->check($request)) {
            return $next($request);
        }

        abort(404);
    }

    /**
     * Checks if the token was issued. If it is, it removes it from the cache.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return bool
     * @throws \Exception
     */
    public function check(Request $request): bool
    {
        $token = $request->input('token_ws') ?? $request->input('TBK_TOKEN');

        return $token && app(Factory::class)->store($this->store)->pull($this->prefix . $token);
    }
}