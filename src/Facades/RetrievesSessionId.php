<?php

namespace DarkGhostHunter\Larabanker\Facades;

trait RetrievesSessionId
{
    /**
     * Retrieves the current request Session ID, or creates a throwaway string.
     *
     * @return string
     */
    protected static function generateSessionId(): string
    {
        $session = static::getFacadeApplication()->make('session.store');

        return $session->isStarted() ? $session->getId() : bin2hex(random_bytes(10));
    }
}