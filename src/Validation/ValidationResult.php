<?php

namespace Stormmore\Framework\Validation;

class ValidationResult
{
    public bool $isValid = true;
    public array $errors = [];

    function addError(string $field, $value): void
    {
        $this->isValid = false;
        $this->errors[$field] = $value;
    }

    function isValid(): bool
    {
        return $this->isValid;
    }

    function getErrors(): array
    {
        return $this->errors;
    }

    function getErrorsAsString($newLine = "\n"): string
    {
        $msg = "";
        foreach($this->errors as $field => $error) {
            $msg .= $field . ": " . $error . $newLine;
        }
        return $msg;
    }

    function getErrorsAsHtml($tag = 'ul'): string
    {
        $msg = $openTag = $closeTag = "";

        $msg = match($tag) {
            "ul" => "<ul>",
            'pre' => "<pre>",
            default => ""
        };
        $openTag = match($tag) {
            "ul" => "<li>",
            "div" => "<div>",
            "p" => "<p>",
            default => ""
        };
        $closeTag = match($tag) {
            "ul" => "</li>",
            "div" => "</div>",
            "pre" => "\n",
            "p" => "</p>",
            default => "",
        };

        foreach($this->errors as $field => $error) {
            $msg .= $openTag . $field . ": " . $error . $closeTag;
        }
        $msg = match($tag) {
            "ul" => $msg . "</ul>",
            "pre" => $msg . "</pre>",
            default => $msg
        };
        return $msg;
    }

    function __get($name)
    {
        $field = new FieldValidationResult();
        if (array_key_exists($name, $this->errors)) {
            $field->invalid = true;
            $field->valid = false;
            $field->message = $this->errors[$name];
        }

        return $field;
    }
}