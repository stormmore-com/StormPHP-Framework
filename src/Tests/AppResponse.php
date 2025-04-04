<?php

namespace Stormmore\Framework\Tests;

class AppResponse
{
    private $headers = [];
    private string $rawHeaders;
    private string $body;

    public function __construct(string $output)
    {
        $this->parseOutput($output);
        $this->parseHeaders();
    }

    public function getStatusCode(): int
    {
        return $this->headers['Status-Code'];
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getJson(): object
    {
        return json_decode($this->body);
    }

    public function getHeader(string $name): ?string
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
        return "";
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    private function parseOutput(string $output): void
    {
        $beginning = strpos($output, "<http-header>");
        $ending = strpos($output, "</http-header>", $beginning) + strlen("</http-header>");
        while(($found = strpos($output, "<http-header>", $ending)) !== false and $found - $ending == 1)
        {
            $ending = strpos($output, "</http-header>", $found) + strlen("</http-header>");
        }
        $ending++;

        $this->rawHeaders = substr($output, $beginning, $ending - $beginning);
        $this->body = substr($output, $ending);
    }

    private function parseHeaders(): void
    {
        foreach (explode("\n", $this->rawHeaders) as $line) {
            $header = str_replace(array("<http-header>", "</http-header>"), "", $line);
            [$name, $value] = explode(":", $header);
            $this->headers[$name] = trim($value);
        }
    }
}