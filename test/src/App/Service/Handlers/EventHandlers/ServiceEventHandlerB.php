<?php

namespace Configuration\Handlers\EventHandlers;

use Configuration\Events\ServiceEvent;
use Stormmore\Framework\Events\EventHandler;
use Stormmore\Framework\Events\IEvent;
use Stormmore\Framework\Events\IEventHandler;

#[EventHandler(ServiceEvent::class)]
class ServiceEventHandlerB implements IEventHandler
{
    public function handle(IEvent $event)
    {
    }
}