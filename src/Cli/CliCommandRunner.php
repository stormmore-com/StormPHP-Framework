<?php

namespace Stormmore\Framework\Cli;

use Exception;
use Stormmore\Framework\App\RequestContext;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Mvc\IO\Response;
use Stormmore\Framework\SourceCode\SourceCode;

readonly class CliCommandRunner
{
    public function __construct(private RequestContext $requestContext,
                                private AppConfiguration $configuration,
                                private SourceCode $sourceCode,
                                private Resolver $resolver,
                                private Response $response)
    {
    }

    public function run(): void
    {
        $cliArguments = $this->requestContext->getCliArguments();
        if (!$cliArguments->hasCommandParameters()) {
            $this->response->setBody("No proper command parameters found.");
            return;
        }

        $this->runTask($cliArguments->getTaskName());
    }

    private function runTask(string $taskName): void
    {
        $tasks = $this->sourceCode->getTasks();
        if (!array_key_exists($taskName, $tasks)) {
            if ($this->configuration->isDevelopment()) {
                $this->sourceCode->scan();
                $tasks = $this->sourceCode->getTasks();
                if (array_key_exists($taskName, $tasks)) {
                    $this->sourceCode->writeCache();
                }
            }
            if(!array_key_exists($taskName, $tasks)) {
                $this->response->setBody("Task '$taskName' is not found");
                return;
            }
        }

        $taskClassName = $tasks[$taskName];
        $task = $this->resolver->resolve($taskClassName);
        if (!$task instanceof ICliTask) {
            $this->response->setBody("Task '$taskName' does not implement CliTask interface");
            return;
        }
        $task->run();
    }
}