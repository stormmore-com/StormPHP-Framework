<?php

namespace Stormmore\Framework\Cli;

use Stormmore\Framework\Http\FormData;

class CliArguments
{
    private $commandParameters = array(
        '-t'
    );
    private $handledFlags = array(
        '-r', '-t', '-m', "-p",
        "-parameters",
        "-method",
        "-headers",
        "-cookies",
        "-content",
        "-content-type",
        "-form",
        "-print-headers");
    private array $arguments = [];

    public function __construct()
    {
        $this->readArguments();
    }

    private function readArguments(): void
    {
        $switch = null;
        for ($i = 1; $i < count($_SERVER['argv']); $i++) {
            $arg = $_SERVER['argv'][$i];
            if (in_array($arg, $this->handledFlags)) {
                $switch = $arg;
                if (!array_key_exists($switch, $this->arguments)) {
                    $this->arguments[$switch] = [];
                }
                continue;
            }
            if ($switch) {
                $this->arguments[$switch][] = $arg;
            }
        }
    }

    public function hasCommandParameters(): bool
    {
        foreach($this->commandParameters as $commandParameter) {
            if (array_key_exists($commandParameter, $this->arguments)) {
                return true;
            }
        }
        return false;
    }

    public function getTaskName(): ?string
    {
        if (array_key_exists('-t', $this->arguments)) {
            return $this->arguments['-t'][0];
        }
        return null;
    }

    public function printHeaders(): bool
    {
        return array_key_exists("-print-headers", $this->arguments);
    }

    public function isRequest(): bool
    {
        return array_key_exists("-r", $this->arguments);
    }

    public function getPath(): string
    {
        if (array_key_exists('-r', $this->arguments)) {
            $uri = $this->arguments['-r'][0];
            if (str_contains($uri, '?')) {
                $uri = substr($uri, 0, strpos($uri, '?'));
            }
            return $uri;
        }
        return "";
    }

    public function getMethod(): string
    {
        if (array_key_exists('-method', $this->arguments)) {
            return strtoupper($this->arguments['-method'][0]);
        }
        return "GET";
    }

    public function getHeaders(): array
    {
        $headers = [];
        if (array_key_exists('-headers', $this->arguments)) {
            foreach ($this->arguments['-headers'] as $header) {
                [$name, $value] = explode(":", $header);
                $headers[$name] = trim($value);
            }
        }
        return $headers;
    }

    public function getCookies(): array
    {
        $cookies = [];
        if (array_key_exists('-cookies', $this->arguments)) {
            foreach ($this->arguments['-cookies'] as $header) {
                [$name, $value] = explode(":", $header);
                $cookies[$name] = trim($value);
            }
        }
        return $cookies;
    }

    public function getQuery(): string
    {
        if (array_key_exists('-r', $this->arguments)) {
            $uri = $this->arguments['-r'][0];
            if (str_contains($uri, '?')) {
                $uri = substr($uri, strpos($uri, '?') + 1);
            }
            return $uri;
        }
        return "";
    }

    public function getGetParameters(): array
    {
        $parameters = [];
        if (array_key_exists('-parameters', $this->arguments)) {
            foreach($this->arguments['-parameters'] as $nameValue) {
                if (str_contains($nameValue, '=')) {
                    list($name, $value) = explode("=", $nameValue);
                    $parameters[$name] = trim($value);
                }
            }
        }
        return $parameters;
    }

    public function getPostParameters(): array
    {
        /** @var FormData $form */
        if (array_key_exists('-form', $this->arguments)) {
            $form = $this->arguments['-form'][0];
            return $form->getNestedFieldsArray();
        }

        return [];
    }

    public function getFiles(): array
    {
        $files = [];
        /** @var FormData $form */
        if (array_key_exists('-form', $this->arguments)) {
            $form = $this->arguments['-form'][0];
            foreach($form->getFiles() as $file) {
                $filepath = $file->getValue();
                $tmpName = tempnam(sys_get_temp_dir(), 'fle');
                copy($filepath, $tmpName);
                $files[$file->getName()] = [
                    'name' => basename($filepath),
                    'tmp_name' => $tmpName,
                    'type' => '',
                    'error' => 0,
                    'size' => filesize($filepath)
                ];
            }
        }
        return $files;
    }

    public function getContentType(): string
    {
        if (array_key_exists('-content-type', $this->arguments)) {
            return $this->arguments['-content-type'][0];
        }
        return "";
    }

    public function getContent(): mixed
    {
        if (array_key_exists('-content', $this->arguments)) {
            return $this->arguments['-content'][0];
        }
        return null;
    }

    public function hasRequestFlag(): bool
    {
        return array_key_exists('-r', $this->arguments);
    }
}