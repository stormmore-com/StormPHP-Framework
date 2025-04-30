<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use DateTime;
use Exception;
use stdClass;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Mvc\IO\Cookie\Cookie;
use Stormmore\Framework\Mvc\IO\Cookie\Cookies;
use Stormmore\Framework\Mvc\IO\Headers\Header;
use Stormmore\Framework\Mvc\IO\Headers\Headers;
use Stormmore\Framework\Mvc\IO\RedirectMessage;
use Stormmore\Framework\Mvc\IO\Request\Parameters\IParameters;
use Stormmore\Framework\Mvc\IO\Request\Parameters\Parameters;

class Request
{
    private Cookies $cookies;
    private IParameters $routeParameters;
    private Headers $headers;
    private string $method;

    public Files $files;
    public string $path;
    public string $query;
    public ?array $acceptedLanguages = [];
    public IParameters $queryParameters;
    public IParameters $postParameters;

    public RedirectMessage $messages;

    function __construct(private readonly RequestContext $context)
    {
        $this->cookies = $this->context->getCookies();
        $this->files = $this->context->getFiles();
        $this->query = $this->context->getQuery();
        $this->path = $this->context->getPath();
        $this->method = $this->context->getMethod();
        $this->queryParameters = $this->context->queryParameters();
        $this->postParameters = $this->context->postParameters();
        $this->headers = $this->context->getHeaders();
        $this->messages = new RedirectMessage($this->cookies);
    }

    public function getReferer(): ?string
    {
        return $this->context->getReferer();
    }

    public function encodeRequestUri(): string
    {
        return urlencode($this->context->getPath());
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
        if ($this->context->getContentType() == "application/json") {
            return json_decode($this->context->getContent());
        }
        return null;
    }

    public function getBody(): mixed
    {
        return $this->context->getContent();
    }

    public function hasCookie(string $name): bool
    {
        return $this->cookies->has($name);
    }

    public function getCookie(string $name): Cookie
    {
        return $this->cookies->get($name);
    }

    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    public function getHeader(string $name): ?Header
    {
        return $this->headers->get($name);
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function has(string $name): bool
    {
        return $this->queryParameters->has($name) or
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
        if ($this->queryParameters->has($name)) {
            return $this->queryParameters->get($name);
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
        return array_merge($this->queryParameters->toArray(),
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
            } catch (Exception) {
            }
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
        $languages = $this->context->getAcceptedLanguages();
        foreach ($languages as $language) {
            if (str_contains($language, ';')) {
                $this->acceptedLanguages[] = new Locale(explode(';', $language)[0]);
            } else {
                $this->acceptedLanguages[] = new Locale($language);
            }
        }

        return $this->acceptedLanguages;
    }

    public function getFirstAcceptedLocale(array $supportedLocales): Locale|null
    {
        $acceptedLanguages = $this->getAcceptedLocales();
        foreach ($acceptedLanguages as $acceptedLanguage) {
            foreach ($supportedLocales as $supportedLocale) {
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