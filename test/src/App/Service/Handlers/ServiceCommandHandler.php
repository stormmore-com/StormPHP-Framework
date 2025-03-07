<?php

namespace Configuration\Handlers;

use Configuration\Commands\ServiceCommand;
use Stormmore\Framework\Cqs\CommandHandler;

#[CommandHandler(ServiceCommand::class)]
class ServiceCommandHandler
{
    public function handle(ServiceCommand $command): void
    {
    }
}