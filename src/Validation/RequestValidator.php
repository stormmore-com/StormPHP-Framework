<?php

namespace Stormmore\Framework\Validation;

use Exception;
use Stormmore\Framework\App;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Request\Request;

class RequestValidator
{
    public Request $request;
    public Resolver $resolver;

    function __construct(Request $request, Resolver $resolver)
    {
        $this->request = $request;
        $this->resolver = $resolver;
    }

    function validate(array $validators): ValidationResult
    {
        $result = new ValidationResult();
        foreach ($validators as $fieldName => $set) {
            if (is_array($set)) {
                foreach ($set as $value) {
                    if ($value instanceof IValidator) {
                        $validator = $value;
                    } else {
                        $validator = $this->instantiateValidator($value);
                    }
                    $this->validateField($fieldName, $result, $validator);
                }
            } else {
                $this->validateField($fieldName, $result, $validator);
            }

        }
        return $result;
    }

    private function validateField(string $fieldName, ValidationResult $result, IValidator $validator): void
    {
        $value = $this->request->getParameter($fieldName);
        $validatorResult = $validator->validate($value, $fieldName, $this->request->parameters, []);
        if (!$validatorResult->valid) {
            $result->addError($fieldName, $validatorResult->message);
        }
    }

    private function instantiateValidator(string $validatorName): IValidator
    {
        //tablica buildInValidator
        if (!str_ends_with($validatorName, "Validator") and !str_contains($validatorName, "\\")) {
            $validatorName = $this->normalizeValidatorName($validatorName);
        }

        $classLoader = App::getInstance()->getClassLoader();
        $fullyQualifiedValidatorName = $classLoader->includeFileByClassName($validatorName);
        if (!class_exists($fullyQualifiedValidatorName)) {
            throw new Exception("Validator $fullyQualifiedValidatorName does not exist");
        }

        $validator = $this->resolver->resolveObject($fullyQualifiedValidatorName);
        if ($validator instanceof IValidator) {
            return $validator;
        }

        throw new Exception("Validator: $validatorName is not a valid validator");
    }

    private function normalizeValidatorName(string $validatorName): string
    {
        $normalizedName = '';
        $len = strlen($validatorName);
        $i = 0;
        while ($i < $len) {
            $char = $validatorName[$i];
            if ($char == '-' or $char == '_') {
                if ($i + 1 < $len) {
                    $validatorName[$i + 1] = strtoupper($validatorName[$i + 1]);
                }
            } else {
                $normalizedName .= $char;
            }
            $i++;
        }

        return ucfirst($normalizedName) . 'Validator';
    }
}