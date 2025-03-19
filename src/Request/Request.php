<?php

namespace Stormmore\Framework\Request;

use stdClass;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Reflection\ObjectReflector;

class Request
{
    public string $uri;
    public string $baseUri;
    /**
     * in case of /path/to/script/index.php/my-module returns /my-module
     */
    public string $requestUri;
    public string $basePath;
    public string $query;
    public ?array $acceptedLanguages = null;
    public array $parameters = [];
    public array $getParameters;
    public array $postParameters;
    public array $routeParameters;
    public Files $files;
    public string $method;
    public object $body;

    public RedirectMessage $messages;

    function __construct(public Cookies $cookies)
    {
        $this->files = new Files();
        $this->messages = new RedirectMessage($cookies);

        $this->query = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : "";
        $this->uri = strtok($_SERVER["REQUEST_URI"], '?');
        $this->requestUri = strtok($_SERVER["REQUEST_URI"], '?');

        $self = $_SERVER['PHP_SELF'];
        $self = substr($self, 0, strrpos($self, '.php') + 4);
        $this->basePath = substr($self, 0, strpos($self, '.php'));
        $this->basePath = substr($this->basePath, 0, strrpos($this->basePath, '/'));
        if (str_starts_with($this->uri, $self)) {
            $this->baseUri = $self;
        } else {
            $this->baseUri = $this->basePath;
        }

        $this->getParameters = $this->cast($_GET);
        $this->postParameters = $this->cast($_POST);
        $this->parameters = array_merge($this->getParameters, $this->postParameters);

        $this->method = $_SERVER['REQUEST_METHOD'];

        if (array_key_exists("CONTENT_TYPE", $_SERVER) && $_SERVER["CONTENT_TYPE"] == "application/json") {
            $data = file_get_contents('php://input');
            $this->body = json_decode($data);
        }

        unset($_GET);
        unset($_POST);
    }


    public function getReferer(): ?string
    {
        $referer = null;
        if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            $referer = $_SERVER['HTTP_REFERER'];
        }
        return $referer;
    }

    public function encodeRequestUri(): string
    {
        return urlencode($_SERVER["REQUEST_URI"]);
    }

    public function decodeParameter(string $name): ?string
    {
        $parameter = $this->getParameter($name);
        if ($parameter) {
            $parameter = urldecode($parameter);
        }
        return $parameter;
    }

    public function addRouteParameters(array $parameters): void
    {
        $this->routeParameters = $parameters;
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    function isPost(): bool
    {
        return $this->method == 'POST';
    }

    function isGet(): bool
    {
        return $this->method == 'GET';
    }

    function isDelete(): bool
    {
        return $this->method == 'DELETE';
    }

    public function isPut(): bool
    {
        return $this->method == 'PUT';
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function hasParameter(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function hasGetParameter(string $name): bool
    {
        return array_key_exists($name, $this->getParameters);
    }

    public function __get(string $name): mixed
    {
        if ($this->files->has($name)) {
            return $this->files->get($name);
        }
        if ($this->hasParameter($name)) {
            return $this->getParameter($name);
        }
        return null;
    }

    public function get(string ...$names): mixed
    {
        if (count($names) == 1) {
            return $this->getParameter($names[0]);
        }

        $parameters = array();
        foreach ($names as $name) {
            $parameters[] = $this->getParameter($name);
        }
        return $parameters;
    }

    public function getParameter(string $name, $defaultValue = null): mixed
    {
        if ($this->hasParameter($name)) {
            $value = $this->parameters[$name];
            return $this->sanitize($value);
        }
        return $defaultValue;
    }

    public function getUnsanitizedParameter(string $name, $defaultValue = null): mixed
    {
        if ($this->hasParameter($name)) {
            return $this->parameters[$name];
        }
        return $defaultValue;
    }

    public function getUnsanitized(string ...$names): mixed
    {
        if (count($names) == 1) {
            return $this->getUnsanitizedParameter($names[0]);
        }

        $parameters = array();
        foreach ($names as $name) {
            $parameters[] = $this->getUnsanitizedParameter($name);
        }
        return $parameters;
    }

    public function getUrlParameter(string $name, $defaultValue = null): mixed
    {
        if ($this->hasGetParameter($name)) {
            return $this->getParameters[$name];
        }
        return $defaultValue;
    }

    public function getInt(string $name, ?int $defaultValue = null): ?int
    {
        if ($this->hasParameter($name) and is_int($this->getParameter($name))) {
            return intval($this->parameters[$name]);
        }

        return $defaultValue;
    }

    /**
     * @return Locale[]
     */
    public function getAcceptedLocales(): array
    {
        if ($this->acceptedLanguages) {
            return $this->acceptedLanguages;
        }

        $this->acceptedLanguages = [];
        if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($languages as $language) {
                if (str_contains($language, ';')) {
                    $this->acceptedLanguages[] = new Locale(explode(';', $language)[0]);
                } else {
                    $this->acceptedLanguages[] = new Locale($language);
                }
            }
        }

        return $this->acceptedLanguages;
    }

    public function getFirstAcceptedLocale(array $supportedLocales): Locale|null
    {
        $acceptedLanguages = $this->getAcceptedLocales();
        foreach ($acceptedLanguages as $acceptedLanguage) {
            foreach($supportedLocales as $supportedLocale) {
                if ($acceptedLanguage->equals($supportedLocale)) {
                    return $supportedLocale;
                }
            }
        }
        return null;
    }

    public function toObject(array|null $map = null): object
    {
        $obj = new stdClass();
        $this->assign($obj, $map);
        return $obj;
    }

    public function assign(object $obj, array|null $map = null): void
    {
        $reflection = new ObjectReflector($obj);
        if ($map == null) {
            foreach ($this->parameters as $name => $value) {
                $reflection->set($name, $value);
            }
        } else {
            foreach ($map as $mapKey => $mapValue) {
                $destinationField = $mapValue;
                if (is_int($mapKey)) {
                    $requestField = $mapValue;
                } else {
                    $requestField = $mapKey;
                }
                $reflection->set($destinationField, $this->getParameter($requestField));
            }
        }
    }

    private function cast(array $values): array
    {
        $typed = [];
        foreach($values as $key => $value) {
            if (is_array($value)) {
                $typed[$key] = $this->cast($value);
            }
            else if (is_string($value)) {
                $strValue = strtolower($value);
                if ($strValue === 'true') {
                    $typed[$key] = true;
                }
                else if ($strValue === 'false') {
                    $typed[$key] = false;
                } else {
                    $typed[$key] = $value;
                }
            }
            else if (is_numeric($value)) {
                $typed[$key] = $value * 1;
            }
        }
        return $typed;
    }

    private function sanitize(string|array $value): mixed
    {
        if (is_array($value)) {
            $values = $value;
            foreach ($values as $key => $value) {
                $values[$key] = htmlspecialchars($value);
            }
            return $values;
        } else {
            return htmlspecialchars($value);
        }
    }
}