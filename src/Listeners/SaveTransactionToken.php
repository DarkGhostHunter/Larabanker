<?php

namespace DarkGhostHunter\Larabanker\Listeners;

use DarkGhostHunter\Transbank\Events\TransactionCreated;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;

class SaveTransactionToken
{
    /**
     * CacheTransactionToken constructor.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $store
     * @param  string  $prefix
     */
    public function __construct(protected Repository $store, protected string $prefix)
    {
        //
    }

    /**
     * Handle the Transaction Created event.
     *
     * @param  \DarkGhostHunter\Transbank\Events\TransactionCreated  $event
     * @return void
     */
    public function handle(TransactionCreated $event): void
    {
        $this->store->put(Str::finish($this->prefix, '|') . $event->response->getToken(), true, 300);
    }
}