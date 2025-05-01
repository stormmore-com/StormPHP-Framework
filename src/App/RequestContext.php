<?php

namespace Stormmore\Framework\App;

use Stormmore\Framework\Cli\CliArguments;
use Stormmore\Framework\Mvc\IO\Cookie\Cookie;
use Stormmore\Framework\Mvc\IO\Cookie\Cookies;
use Stormmore\Framework\Mvc\IO\Headers\Header;
use Stormmore\Framework\Mvc\IO\Headers\Headers;
use Stormmore\Framework\Mvc\IO\Request\Files;
use Stormmore\Framework\Mvc\IO\Request\Parameters\IParameters;
use Stormmore\Framework\Mvc\IO\Request\Parameters\Parameters;

class RequestContext
{
    private CliArguments $arguments;
    private ?string $contentType;
    private Cookies $cookies;
    private Headers $headers;
    private bool $printHeaders = false;
    private bool $isCli = false;
    private bool $isCliRequest = false;
    private bool $isCliCommand = false;
    private string $path;
    private string $query;
    private string $method;
    private IParameters $get;
    private IParameters $post;
    private Files $files;


    public function __construct()
    {
        if (php_sapi_name() === 'cli') {
            $this->isCli = true;
            $this->arguments = $arg = new CliArguments();


            $this->printHeaders = $arg->printHeaders();
            $this->path = $arg->getPath();
            $this->query = $arg->getQuery();
            $this->method = $arg->getMethod();
            parse_str($this->getQuery(), $result);
            $this->get = new Parameters($result);
            $this->post = new Parameters($arg->getPostParameters());
            $this->contentType = $arg->getContentType();
            $cookies = $arg->getCookies();
            $headers = $arg->getHeaders();
            $this->files = new Files($arg->getFiles());

            if ($arg->isRequest()) {
                $this->isCliRequest = true;
            }
            else {
                $this->isCliCommand = true;
            }
        }
        else {
            $this->path = strtok($_SERVER["REQUEST_URI"], '?');
            $this->query = array_key_value($_SERVER, "QUERY_STRING", "");
            $this->method = $_SERVER["REQUEST_METHOD"];
            $this->get = new Parameters($_GET);
            $this->post = new Parameters($_POST);
            $this->contentType = array_key_value($_SERVER, 'CONTENT_TYPE', '');
            $headers = getallheaders();
            $cookies = $_COOKIE;
            $this->files = new Files($_FILES);
        }

        $_headers = [];
        foreach($headers as $name => $value) {
            $_headers[$name] = new Header($name, $value);
        }
        $this->headers = new Headers($_headers);

        $_cookies = [];
        foreach($cookies as $name => $value) {
            $_cookies[$name] = new Cookie($name, $value);
        }
        $this->cookies = new Cookies($_cookies);
    }

    public function getTaskName(): ?string
    {
        return $this->arguments->getTask();
    }

    public function isCli(): bool
    {
        return $this->isCli;
    }

    public function isCliCommand(): bool
    {
         return $this->isCliCommand;
    }

    public function isCliRequest(): bool
    {
        return $this->isCli and $this->isCliRequest;
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

    public function getHeaders(): Headers
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
        return $this->files;
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
        return $this->post;
    }
}