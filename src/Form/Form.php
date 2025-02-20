<?php

namespace Stormmore\Framework\Form;
use Stormmore\Framework\Validation\ValidationResult;

class Form
{
    public Request $request;
    public array|object|null $model;
    public array $rules;
    public ?ValidationResult $validationResult = null;

    function __construct($request, object $model = null)
    {
        $this->request = $request;
        $this->model = $model;
    }

    function addRules(array $rules): void
    {
        $this->rules = $rules;
    }

    function removeRule(string $field, string $name): void
    {
        if (array_key_exists($field, $this->rules) and array_key_exists($name, $this->rules[$field])) {
            unset($this->rules[$field][$name]);
        }
        if (array_key_exists($field, $this->rules) and ($key = array_search($name, $this->rules[$field])) !== false) {
            unset($this->rules[$field][$key]);
        }
    }

    function printError($name, string $message = null): void
    {
        if ($this->validationResult != null) {
            $field = $this->validationResult->__get($name);
            $message = empty($message) ? $field->message : $message;
            echo html::error($field->valid, $message);
        }
    }

    function hasError($name): ?bool
    {
        return $this->validationResult?->__get($name)->invalid;
    }

    function getError($name): ?string
    {
        return $this->validationResult?->__get($name)?->message;
    }

    function printIfError(string $name, string $present): void
    {
        if ($this->hasError($name))
            echo $present;
    }

    function printIfElseError(string $name, string $present, string $notPresent): void
    {
        if ($this->hasError($name))
            echo $present;
        else
            echo $notPresent;
    }

    function validate(): ValidationResult
    {
        $this->validationResult = $this->request->validate($this->rules);
        return $this->validationResult;
    }

    function isValid(): bool
    {
        return $this->validationResult?->isValid();
    }

    function isInvalid(): bool
    {
        return $this->validationResult != null and !$this->validationResult->isValid();
    }

    function isSubmittedSuccessfully(): bool
    {
        return $this->request->isPost() and $this->validate()->isValid();
    }
}