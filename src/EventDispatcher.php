<?php

namespace DarkGhostHunter\Larabanker;

use Illuminate\Contracts\Events\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * The application event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * EventDispatcher constructor.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param  object  $event  The object to process.
     *
     * @return void
     */
    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}