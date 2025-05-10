<?php

namespace Stormmore\Framework\Tests;

class TestWebServer
{
    private $process;

    public function __construct(private readonly string $directory, private readonly int $port)
    {
    }

    public function run(): void
    {
        $cwd = getcwd();
        chdir($this->directory);

        $pipes = [];
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $cmd = "php -S localhost:{$this->port}";
        $this->process = proc_open($cmd, $descriptors, $pipes);
        if (is_resource($this->process)) {
           fclose($pipes[1]);
           fclose($pipes[2]);
           fclose($pipes[0]);
        }

        chdir($cwd);
    }

    function __destruct()
    {
        $this->shutdown();
    }

    public function shutdown(): void
    {
        if ($this->process) {
             proc_terminate($this->process);
        }
    }
}