<?php

namespace Discodian\Sentry\Listeners;

use Discodian\Core\Events\Log\RegistersHandlers;
use Illuminate\Contracts\Events\Dispatcher;
use Monolog\Handler\RavenHandler;
use Raven_Client;

class RegisterHandler
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(RegistersHandlers::class, [$this, 'register']);
    }

    public function register(RegistersHandlers $event)
    {
        if ($dsn = env('SENTRY_DSN')) {
            $client = new Raven_Client($dsn);

            $handler = new RavenHandler($client);

            $handler->setFormatter($event->formatter);

            $event->handlers[] = $handler;
        }
    }
}
