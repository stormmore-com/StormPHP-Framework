<?php

namespace app\src\Infrastructure\Tasks;

use Stormmore\Framework\Cli\CliTask;
use Stormmore\Framework\Cli\ICliTask;
use Stormmore\Framework\Mvc\IO\Response;

#[CliTask("send-emails")]
class SendEmails implements ICliTask
{
    public function __construct(private Response $response)
    {
    }

    public function run(): void
    {
        $this->response->setBody("Send emails...");
    }
}