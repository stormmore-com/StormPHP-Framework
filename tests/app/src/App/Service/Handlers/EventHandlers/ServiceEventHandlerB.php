<?php

namespace src\App\Service\Handlers\EventHandlers;

use src\App\Service\Events\ServiceEvent;
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