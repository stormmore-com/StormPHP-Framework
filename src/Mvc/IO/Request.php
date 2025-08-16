<?php

namespace Stormmore\Framework\Mvc\IO;

use DateTime;
use Exception;
use stdClass;
use Stormmore\Framework\App\RequestContext;
use Stormmore\Framework\Http\Interfaces\ICookie;
use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Mvc\IO\Cookie\Cookies;
use Stormmore\Framework\Mvc\IO\Headers\Headers;
use Stormmore\Framework\Mvc\IO\Request\Files;
use Stormmore\Framework\Mvc\IO\Request\Parameters\IParameters;
use Stormmore\Framework\Mvc\IO\Request\Parameters\Parameters;
use Stormmore\Framework\Mvc\IO\Request\RequestMapper;

class Request
{
    private Cookies $cookies;
    private IParameters $routeParameters;
    private Headers $headers;
    public string $method;

    public Files $files;
    public string $path;
    public string $queryString;
    public ?array $acceptedLanguages = [];
    public IParameters $query;
    public IParameters $post;

    public RedirectMessage $messages;

    function __construct(private readonly RequestContext $context)
    {
        $this->cookies = $this->context->getCookies();
        $this->files = $this->context->getFiles();
        $this->queryString = $this->context->getQuery();
        $this->path = $this->context->getPath();
        $this->method = $this->context->getMethod();
        $this->query = $this->context->queryParameters();
        $this->post = $this->context->postParameters();
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

    function is(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    function isGet(): bool
    {
        return $this->method == 'GET';
    }

    function isPost(): bool
    {
        return $this->method == 'POST';
    }

    public function isPut(): bool
    {
        return $this->method == 'PUT';
    }

    public function isPatch(): bool
    {
        return $this->method == 'PATCH';
    }

    function isDelete(): bool
    {
        return $this->method == 'DELETE';
    }

    public function json(): ?object
    {
        if ($this->context->getContentType() == "application/json") {
            return json_decode($this->context->getContent());
        }
        return null;
    }

    public function body(): mixed
    {
        return $this->context->getContent();
    }

    public function hasCookie(string $name): bool
    {
        return $this->cookies->has($name);
    }

    public function getCookie(string $name): ICookie
    {
        return $this->cookies->get($name);
    }

    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    public function getHeader(string $name): null|IHeader
    {
        return $this->headers->get($name);
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function has(string $name): bool
    {
        return $this->query->has($name) or
            $this->post->has($name) or
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
        if ($this->query->has($name)) {
            return $this->query->get($name);
        }
        if ($this->post->has($name)) {
            return $this->post->get($name);
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
        return array_merge($this->query->toArray(),
            $this->post->toArray(),
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

    /**
     * @return Locale[]
     */
    public function getLocales(): array
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
        $acceptedLanguages = $this->getLocales();
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