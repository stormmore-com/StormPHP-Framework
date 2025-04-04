<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use Stormmore\Framework\Mvc\IO\Cookie\Cookies;

class RequestContext
{
    private bool $printHeaders = false;
    private bool $isCliRequest = false;

    private string $path;
    private string $query;
    private string $method;
    private IParameters $get;


    public function __construct()
    {
        if (php_sapi_name() === 'cli') {
            $this->isCliRequest = true;
            $arg = new RequestArguments();
            $this->printHeaders = $arg->printHeaders();
            $this->path = $arg->getPath();
            $this->query = $arg->getQuery();
            $this->method = $arg->getMethod();
            parse_str($this->getQuery(), $result);
            $this->get = new Parameters($result);
        }
        else {
            $this->path = strtok($_SERVER["REQUEST_URI"], '?');
            $this->query = array_key_value($_SERVER, "QUERY_STRING", "");
            $this->method = $_SERVER["REQUEST_METHOD"];
            $this->get = new Parameters($_GET);
        }
    }

    public function isCliRequest(): bool
    {
        return $this->isCliRequest;
    }

    public function printHeaders(): bool
    {
        return $this->printHeaders;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAcceptedLanguages(): array
    {
        $acceptedLanguage = array_key_value($_SERVER, 'HTTP_ACCEPT_LANGUAGE', '');
        return explode(',', $acceptedLanguage);
    }

    public function getContentType(): string
    {
        return array_key_value($_SERVER, 'CONTENT_TYPE', '');
    }

    public function getContent(): string
    {
        return file_get_contents('php://input');
    }

    public function getReferer(): string
    {
        return array_key_value($_SERVER, 'HTTP_REFERER', '');
    }

    public function getFiles(): Files
    {
        return new Files();
    }

    public function getCookies(): Cookies
    {
        return new Cookies();
    }

    public function queryParameters(): IParameters
    {
        return $this->get;
    }

    public function postParameters(): IParameters
    {
        return new Parameters($_POST);
    }
}