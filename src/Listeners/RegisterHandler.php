<?php

/*
 * This file is part of the Discodian bot toolkit.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://discodian.com
 * @see https://github.com/discodian
 */

namespace Discodian\Sentry\Listeners;

use Discodian\Core\Events\Log\RegistersHandlers;
use Illuminate\Contracts\Events\Dispatcher;
use Monolog\Handler\RavenHandler;
use Monolog\Logger;
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
            $options = [];

            if ($release = env('SENTRY_RELEASE')) {
                $options['release'] = $release;
            }
            if ($tags = env('SENTRY_TAGS')) {
                $options['tags'] = explode(',', $tags);
            }

            $options['app_path'] = base_path();

            $client = new Raven_Client($dsn, $options);

            $handler = new RavenHandler($client, env('SENTRY_LEVEL', Logger::ERROR));

            $handler->setFormatter($event->formatter);

            array_unshift($event->handlers, $handler);
        }
    }
}
