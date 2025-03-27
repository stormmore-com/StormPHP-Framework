<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use Stormmore\Framework\Mvc\IO\Cookie\Cookies;

class RequestCreator
{
    public static function create(): Request
    {
        $cookies = new Cookies();
        $files = new Files();

        return new Request($cookies, $files, self::getParameters(), self::postParameters(), self::allParameters());
    }

    private static function getParameters(): IParameters
    {
        return new Parameters($_GET);
    }

    private static function postParameters(): IParameters
    {
        return new Parameters($_POST);
    }

    private static function allParameters(): IParameters
    {
        return new Parameters($_GET, $_POST);
    }
}