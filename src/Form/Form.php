<?php

namespace Stormmore\Framework\Form;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Validation\ValidationResult;

class Form
{
    public Request $request;
    public array|object|null $model;
    public array $validators;
    public ?ValidationResult $validationResult = null;

    function __construct(Request $request, object|null $model = null)
    {
        $this->request = $request;
        $this->model = $model;
    }

    public function addValidators(array $validators): void
    {
        $this->validators = $validators;
    }

    public function removeValidator(string $field, string $name): void
    {
        if (array_key_exists($field, $this->validators) and array_key_exists($name, $this->validators[$field])) {
            unset($this->validators[$field][$name]);
        }
        if (array_key_exists($field, $this->validators) and ($key = array_search($name, $this->validators[$field])) !== false) {
            unset($this->validators[$field][$key]);
        }
    }

    function printError($name, string|null $message = null): void
    {
        if ($this->validationResult != null) {
            $field = $this->validationResult->__get($name);
            $message = empty($message) ? $field->message : $message;
            echo html_error($field->valid, $message);
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
        $this->validationResult = $this->request->validate($this->validators);
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