<?php

namespace Stormmore\Framework\Request;

use stdClass;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Reflection\ObjectReflector;
use Stormmore\Framework\Validation\RequestValidator;
use Stormmore\Framework\Validation\ValidationResult;

class Request
{
    private RequestValidator $requestValidator;

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

    /**
     * @type UploadedFile[]
     */
    public array $files;
    public string $method;
    public object $body;

    public RedirectMessage $messages;

    function __construct(public Cookies $cookies, Resolver $codeResolver)
    {
        $this->messages = new RedirectMessage($cookies);
        $this->requestValidator = new RequestValidator($this, $codeResolver);

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

        $this->getParameters = $this->castToTypes($_GET);
        $this->postParameters = $this->castToTypes($_POST);
        $this->parameters = array_merge($_GET, $_POST);

        $this->method = $_SERVER['REQUEST_METHOD'];

        if (array_key_exists("CONTENT_TYPE", $_SERVER) && $_SERVER["CONTENT_TYPE"] == "application/json") {
            $data = file_get_contents('php://input');
            $this->body = json_decode($data);
        }

        $this->files = $this->parseFiles();

        unset($_GET);
        unset($_POST);
    }

    private function parseFiles(): array
    {
        $files = array();
        foreach ($_FILES as $formFieldName => $formFieldFiles) {
            if (is_array($formFieldFiles['name'])) {
                $size = count($formFieldFiles['name']);
                $files[$formFieldName] = array();
                for ($i = 0; $i < $size; $i++) {
                    $files[$formFieldName][$i] = new UploadedFile(
                        $formFieldName,
                        $formFieldFiles['name'][$i],
                        $formFieldFiles['tmp_name'][$i],
                        $formFieldFiles['type'][$i],
                        $formFieldFiles['error'][$i],
                        $formFieldFiles['size'][$i]);
                }
            } else {
                $files[$formFieldName] = new UploadedFile(
                    $formFieldName,
                    $formFieldFiles['name'],
                    $formFieldFiles['tmp_name'],
                    $formFieldFiles['type'],
                    $formFieldFiles['error'],
                    $formFieldFiles['size']);
            }
        }

        return $files;
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
     * @param string $name
     * @return UploadedFile|null
     */
    public function getFile(string $name): UploadedFile|null
    {
        foreach ($this->files as $file) {
            if ($file->fieldName == $name) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return UploadedFile[]
     */
    public function getFiles(string $name): array
    {
        $files = array();
        foreach ($this->files as $file) {
            if ($file->fieldName == $name) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * @param string $name
     * @return bool
     * Check whether request has uploaded valid file
     */
    public function hasFile(string $name): bool
    {
        return $this->getFile($name)?->isUploaded() ?? false;
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

    function toView($data = null): array
    {
        $this->parameters['validation'] = $this->validationResult;

        if ($data != null) {
            if (is_object($data)) {
                $data = (array)$data;
            }
            if (is_array($data)) {
                return array_merge($data, $this->parameters);
            }
        }

        return $this->parameters;
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

    public function validate($rules): ValidationResult
    {
        return $this->requestValidator->validate($rules);
    }

    private function castToTypes(array $values): array
    {
        $typed = [];
        foreach($values as $key => $value) {
            if (is_array($value)) {
                $typed[$key] = $this->castToTypes($value);
            } else {
                if (is_numeric($value)) {
                    $value = $value * 1;
                } else if (strtolower($value) === 'true' || strtolower($value) === 'false') {
                    $value = ($value == 'true');
                }
                $typed[$key] = $value;
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