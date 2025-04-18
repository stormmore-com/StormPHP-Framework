<?php

namespace Stormmore\Framework\Tests;

use Stormmore\Framework\Http\Cookie;
use Stormmore\Framework\Http\Header;
use Stormmore\Framework\Http\IHeader;
use Stormmore\Framework\Http\IResponse;

class AppResponse implements IResponse
{
    private $cookies = [];
    private $headers = [];
    private string $rawHeaders;
    private string $body;

    public function __construct(string $output)
    {
        $this->parseOutput($output);
        $this->parseHeaders();
        $this->parseCookies();
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

    public function getHeader(string $name): ?IHeader
    {
        if (array_key_exists($name, $this->headers)) {
            return new Header($name, $this->headers[$name]);
        }
        return null;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getCookie(string $name): ?Cookie
    {
        if (array_key_exists($name, $this->cookies)) {
            return $this->cookies[$name];
        }
        return null;
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
            if (empty($line)) {
                continue;
            }
            $header = str_replace(array("<http-header>", "</http-header>"), "", $line);
            [$name, $value] = explode(":", $header);
            $name = trim($name);
            $value = trim($value);
            if ($name == 'Set-Cookie') {
                $cookie = explode(";", $value);
                [$name, $value] = explode("=", $cookie[0]);
                $this->cookies[$name] = new Cookie($name, $value);
            }
            else {
                $this->headers[$name] = $value;
            }

        }
    }

    private function parseCookies(): void
    {
//        if (array_key_exists("Set-Cookie", $this->rawHeaders)) {
//
//        }
    }
}