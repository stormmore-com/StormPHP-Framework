<?php

namespace Configuration;

use Stormmore\Framework\Cqs\CommandHandler;
use Stormmore\Framework\Cqs\ICommandHandler;

#[CommandHandler(AddUserCommand::class)]
class AddUserHandler implements ICommandHandler
{
    public function handle(AddUserCommand $command): void
    {
    }
}