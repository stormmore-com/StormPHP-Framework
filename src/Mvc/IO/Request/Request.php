<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use DateTime;
use stdClass;
use Exception;
use Stormmore\Framework\FluentReflection\ObjectPropertyAccess;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Mvc\IO\Cookie\Cookies;
use Stormmore\Framework\Mvc\IO\RedirectMessage;

class Request
{
    private IParameters $routeParameters;
    private string $method;

    public string $uri;
    public string $baseUri;
    /**
     * in case of /path/to/script/index.php/my-module returns /my-module
     */
    public string $requestUri;
    public string $basePath;
    public string $query;
    public ?array $acceptedLanguages = null;

    public RedirectMessage $messages;

    function __construct(public Cookies $cookies,
                         public Files $files,
                         public IParameters $getParameters,
                         public IParameters $postParameters)
    {
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

        $this->method = $_SERVER['REQUEST_METHOD'];

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
        $parameter = $this->get($name);
        if ($parameter) {
            $parameter = urldecode($parameter);
        }
        return $parameter;
    }

    public function addRouteParameters(array $parameters): void
    {
        $this->routeParameters = new Parameters($parameters);
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

    public function getJson(): ?object
    {
        if (array_key_exists("CONTENT_TYPE", $_SERVER) && $_SERVER["CONTENT_TYPE"] == "application/json") {
            $data = file_get_contents('php://input');
            return json_decode($data);
        }
        return null;
    }

    public function has(string $name): bool
    {
        return $this->getParameters->has($name) or
                $this->postParameters->has($name) or
                $this->routeParameters->has($name) or
                $this->files->has($name);
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function get(string $name): mixed
    {
        if ($this->files->has($name)) {
            return $this->files->get($name);
        }
        if ($this->getParameters->has($name)) {
            return $this->getParameters->get($name);
        }
        if ($this->postParameters->has($name)) {
            return $this->postParameters->get($name);
        }
        if ($this->routeParameters->has($name)) {
            return $this->routeParameters->get($name);
        }
        return null;
    }

    public function getMany(string ...$names): mixed
    {
        if (count($names) == 1) {
            return $this->get($names[0]);
        }

        $parameters = array();
        foreach ($names as $name) {
            $parameters[] = $this->get($name);
        }
        return $parameters;
    }

    public function getDefault(string $name, $defaultValue = null): mixed
    {
        if ($this->has($name)) {
            return $this->get($name);
        }
        return $defaultValue;
    }

    public function getAll(): array
    {
        return array_merge($this->getParameters->toArray(),
            $this->postParameters->toArray(),
            $this->files->toArray(),
            $this->routeParameters->toArray());
    }

    public function getUnsanitized(string $name, $defaultValue = null): mixed
    {
        if ($this->has($name)) {
            return $this->parameters[$name];
        }
        return $defaultValue;
    }

    public function getBool(string $name, ?bool $default = null): ?bool
    {
        if ($this->has($name)) {
            $value = strtolower($this->get($name));
            if ($value == "true" or $value == "1") return true;
            if ($value == "false" or $value == "0") return false;
        }

        return $default;
    }

    public function getInt(string $name, ?int $defaultValue = null): ?int
    {
        $value = $this->get($name);
        if ($value and is_numeric($value)) {
            return intval($value);
        }
        return null;
    }

    public function getFloat(string $name, ?float $defaultValue = null): ?float
    {
        $value = $this->get($name);
        if ($value and is_numeric($value)) {
            return floatval($value);
        }
        return null;
    }

    public function getDateTime(string $name, ?DateTime $defaultValue = null): ?DateTime
    {
        $value = $this->get($name);
        if ($value) {
            try {
                return new DateTime($value);
            } catch (Exception) { }
        }
        return null;
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
        RequestMapper::map($this, $obj, $map);
    }
}