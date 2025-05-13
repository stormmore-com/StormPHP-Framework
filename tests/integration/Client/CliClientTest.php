<?php

namespace Client;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Tests\Client\AppResponse;

class CliClientTest extends TestCase
{
    public function testGetRequest(): void
    {
        $this->runCommand("-r /test/get");
    }

    public function testPostRequest(): void
    {
        $this->runCommand("-r /test/post -method POST");
    }

    private function runCommand(string $command): AppResponse
    {
        $cwd = getcwd();
        chdir("app/public_html");
        exec("php index.php $command", $output);
        chdir($cwd);

        return new AppResponse(implode("\n", $output));
    }
}