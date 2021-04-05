<?php

namespace DarkGhostHunter\Larabanker\Listeners;

use DarkGhostHunter\Transbank\Events\TransactionCreated;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;

class SaveTransactionToken
{
    /**
     * Cache repository.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Prefix for the cache key.
     *
     * @var string
     */
    protected $prefix;

    /**
     * CacheTransactionToken constructor.
     *
     * @param  \Illuminate\Contracts\Cache\Factory  $cache
     * @param  \Illuminate\Contracts\Config\Repository  $config
     */
    public function __construct(Factory $cache, Repository $config)
    {
        $this->cache = $cache->store($config->get('larabanker.cache'));
        $this->prefix = Str::finish($config->get('larabanker.cache_prefix'), '|');
    }

    /**
     * Handle the Transaction Created event.
     *
     * @param  \DarkGhostHunter\Transbank\Events\TransactionCreated  $event
     */
    public function handle(TransactionCreated $event)
    {
        $this->cache->put($this->prefix . $event->response->getToken(), true, 300);
    }
}