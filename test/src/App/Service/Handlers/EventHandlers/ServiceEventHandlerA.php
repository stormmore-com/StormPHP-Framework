<?php

namespace Configuration\Handlers\EventHandler;

use Configuration\Events\ServiceEvent;
use Stormmore\Framework\Events\EventHandler;
use Stormmore\Framework\Events\IEventHandler;

#[EventHandler(ServiceEvent::class)]
class ServiceEventHandlerA implements IEventHandler
{
    public function handle(ServiceEvent $event)
    {
    }
}