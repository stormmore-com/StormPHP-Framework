<?php

namespace src\App\Service\Handlers\CommandHandlers;

use src\App\Service\Commands\ServiceCommand;
use Stormmore\Framework\Cqs\CommandHandler;

#[CommandHandler(ServiceCommand::class)]
class ServiceCommandHandler
{
    public function handle(ServiceCommand $command): void
    {
    }
}