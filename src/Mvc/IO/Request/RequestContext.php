<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use Stormmore\Framework\Mvc\IO\Cookie\Cookies;

class RequestContext
{
    public function getUri(): string
    {
        return strtok($_SERVER["REQUEST_URI"], '?');
    }

    public function getQuery(): string
    {
        return array_key_value($_SERVER, 'QUERY_STRING', '');
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
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
        return new Parameters($_GET);
    }

    public function postParameters(): IParameters
    {
        return new Parameters($_POST);
    }
}