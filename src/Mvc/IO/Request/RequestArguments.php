<?php

namespace Stormmore\Framework\Mvc\IO\Request;

class RequestArguments
{
    private $handledFlags = array('-r', '-m', "-p", "-print-headers");
    private array $flagParameters = [];

    public function __construct()
    {
        $this->readArguments();
    }

    private function readArguments(): void
    {
        $switch = null;
        for($i = 1; $i < count($_SERVER['argv']); $i++) {
            $arg = $_SERVER['argv'][$i];
            if (in_array($arg, $this->handledFlags)) {
                $switch = $arg;
                if (!array_key_exists($switch, $this->flagParameters)) {
                    $this->flagParameters[$switch] = [];
                }
                continue;
            }
            if ($switch) {
                $this->flagParameters[$switch][] = $arg;
            }
        }
    }

    public function printHeaders(): bool
    {
        return array_key_exists("-print-headers", $this->flagParameters);
    }

    public function getPath(): string
    {
        if (array_key_exists('-r', $this->flagParameters)) {
            $uri = $this->flagParameters['-r'][0];
            if (str_contains($uri, '?')) {
                $uri = substr($uri, 0, strpos($uri, '?'));
            }
            return $uri;
        }
        return "";
    }

    public function getMethod(): string
    {
        return "";
    }

    public function getQuery(): string
    {
        if (array_key_exists('-r', $this->flagParameters)) {
            $uri = $this->flagParameters['-r'][0];
            if (str_contains($uri, '?')) {
                $uri = substr($uri, strpos($uri, '?') + 1);
            }
            return $uri;
        }
        return "";
    }

    public function hasRequestFlag(): bool
    {
        return array_key_exists('-r', $this->flagParameters);
    }
}