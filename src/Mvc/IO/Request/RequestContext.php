<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use Stormmore\Framework\Mvc\IO\Cookie\Cookie;
use Stormmore\Framework\Mvc\IO\Cookie\Cookies;
use Stormmore\Framework\Mvc\IO\Request\Parameters\IParameters;
use Stormmore\Framework\Mvc\IO\Request\Parameters\Parameters;

class RequestContext
{
    private RequestCliArguments $arguments;
    private ?string $contentType;
    private Cookies $cookies;
    private array $headers = [];
    private bool $printHeaders = false;
    private bool $isCliRequest = false;

    private string $path;
    private string $query;
    private string $method;
    private IParameters $get;


    public function __construct()
    {
        $this->cookies = new Cookies();

        if (php_sapi_name() === 'cli') {
            $this->isCliRequest = true;

            $this->arguments = $arg = new RequestCliArguments();
            $this->printHeaders = $arg->printHeaders();
            $this->path = $arg->getPath();
            $this->query = $arg->getQuery();
            $this->method = $arg->getMethod();
            parse_str($this->getQuery(), $result);
            $this->get = new Parameters($result);
            $this->contentType = $arg->getContentType();
            foreach($arg->getHeaders() as $name => $value) {
                $this->headers[$name] = new Header($name, $value);
            }
            foreach($arg->getCookies() as $name => $value) {
                $this->cookies->add(new Cookie($name, $value));
            }
            $arg->getPostParameters();
        }
        else {
            $this->path = strtok($_SERVER["REQUEST_URI"], '?');
            $this->query = array_key_value($_SERVER, "QUERY_STRING", "");
            $this->method = $_SERVER["REQUEST_METHOD"];
            $this->get = new Parameters($_GET);
            $this->contentType = array_key_value($_SERVER, 'CONTENT_TYPE', '');
            foreach (getallheaders() as $name => $value) {
                $this->headers[$name] = new Header($name, $value);
            }
            foreach($_COOKIE as $name => $value) {
                $this->cookies->add(new Cookie($name, $value));
            }
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

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getAcceptedLanguages(): array
    {
        $acceptedLanguage = array_key_value($_SERVER, 'HTTP_ACCEPT_LANGUAGE', '');
        return explode(',', $acceptedLanguage);
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContent(): string
    {
        if ($this->isCliRequest()) {
            return $this->arguments->getContent();
        }
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
        return $this->cookies;
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