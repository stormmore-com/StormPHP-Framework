<?php

namespace Stormmore\Framework\Route;

class ExecutionRoute
{
    public Endpoint $endpoint;
    public string $pattern;

    public array $parameters;

    function __construct(string $pattern, Endpoint $execution, array $parameters = array())
    {
        $this->pattern = $pattern;
        $this->endpoint = $execution;
        $this->parameters = $parameters;
    }
}