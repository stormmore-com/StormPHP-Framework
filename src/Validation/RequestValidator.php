<?php

namespace Stormmore\Framework\Validation;

use Exception;
use Stormmore\Framework\App;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Request\Request;

class RequestValidator
{
    public Request $request;
    public Resolver $codeAssembler;

    function __construct(Request $request, Resolver $codeAssembler)
    {
        $this->request = $request;
        $this->codeAssembler = $codeAssembler;
    }

    function validate($rules): ValidationResult
    {
        $result = new ValidationResult();
        foreach ($rules as $fieldName => $subrules) {
            $value = $this->request->getParameter($fieldName);
            foreach ($subrules as $subruleKey => $subruleValue) {
                if (!is_int($subruleKey)) {
                    $validatorKey = $subruleKey;
                    $arguments = $subruleValue;
                }
                if (is_int($subruleKey)) {
                    $validatorKey = $subruleValue;
                    $arguments = [];
                }

                if ($validatorKey instanceof IValidator) {
                    $validator = $validatorKey;
                } else {
                    $validator = $this->instantiateValidator($validatorKey);
                }
                $validatorResult = $validator->validate($value, $fieldName, $this->request->parameters, $arguments);
                if (!$validatorResult->valid) {
                    $result->addError($fieldName, $validatorResult->message);
                    break;
                }
            }
        }
        return $result;
    }

    private function instantiateValidator($validatorName): IValidator
    {
        if (!str_ends_with($validatorName, "Validator") and !str_contains($validatorName, "\\")) {
            $validatorName = $this->normalizeValidatorName($validatorName);
        }

        $classLoader = App::getInstance()->getClassLoader();
        $fullyQualifiedValidatorName = $classLoader->includeFileByClassName($validatorName);
        if (!class_exists($fullyQualifiedValidatorName)) {
            throw new Exception("Validator $fullyQualifiedValidatorName does not exist");
        }

        $validator = $this->codeAssembler->resolveObject($fullyQualifiedValidatorName);
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