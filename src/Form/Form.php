<?php

namespace Stormmore\Framework\Form;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Validation\ValidationResult;
use Stormmore\Framework\Validation\Validator;

class Form
{
    public Errors $errors;

    public Request $request;
    public null|array $model;
    protected Validator $validator;
    private null|ValidationResult $validationResult;


    function __construct(Request $request, Validator $validator)
    {
        $this->validationResult = new ValidationResult();
        $this->validator = $validator;
        $this->request = $request;
        $this->errors = new Errors();
    }

    function validate(): ValidationResult
    {
        $this->validationResult = $this->validator->validate($this->request);
        $this->errors->setErrors($this->validationResult);
        return $this->validationResult;
    }

    function setModel(array|object $model): void
    {
        if (is_object($model)) {
            $model = get_object_vars($model);
        }
        $this->model = $model;
    }

    public function getValue(string $name): mixed
    {
        if ($this->request->has($name)) {
            return $this->request->get($name);
        }
        if (array_key_exists($name, $this->model)) {
            return $this->model[$name];
        }
        return null;
    }

    public function __get(string $name): mixed
    {
        return $this->getValue($name);
    }

    function isValid(): bool
    {
        return $this->validationResult?->isValid() === true;
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