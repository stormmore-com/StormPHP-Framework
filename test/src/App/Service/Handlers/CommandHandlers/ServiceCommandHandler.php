<?php

namespace Configuration\Handlers\CommandHandlers;

use Configuration\Commands\ServiceCommand;
use Stormmore\Framework\Cqs\CommandHandler;

#[CommandHandler(ServiceCommand::class)]
class ServiceCommandHandler
{
    public function handle(ServiceCommand $command): void
    {
    }
}