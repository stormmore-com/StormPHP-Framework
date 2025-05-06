<?php

namespace app\src\Infrastructure\Tasks;

use Stormmore\Framework\Cli\CliTask;
use Stormmore\Framework\Cli\ICliTask;
use Stormmore\Framework\Mvc\IO\Response;

#[CliTask("clean-queue")]
class CleanQueue implements ICliTask
{
    public function __construct(private Response $response)
    {
    }

    public function run(): void
    {
        $this->response->setBody("Cleaning queue...");
    }
}