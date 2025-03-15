<?php

namespace Stormmore\Framework\Validation;

use Exception;
use Stormmore\Framework\App;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Validation\Validators\RequiredValidator;

class Validator
{
    private array $fields;

    function __construct(private Resolver $resolver)
    {
        $this->fields = array();
    }

    public function for(string $name): Field
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }
        $field = new Field($name);
        $this->fields[] = $field;
        return $field;
    }

    public function validate(object $object): ValidationResult
    {
        $result = new ValidationResult();
        foreach ($this->fields as $field) {
            $name = $field->getName();
            $value = $object->{$name};
            foreach ($field->getValidators() as $validator) {
                if (!($validator instanceof IValidator)) {
                    $validator = $this->instantiateValidator($validator);
                }
                $validatorResult = $validator->validate($value, $name, array(), []);
                if (!$validatorResult->isValid) {
                    $result->addError($name, $validatorResult->message);
                }
            }
        }
        return $result;
    }

    private function instantiateValidator(string $validatorName): IValidator
    {
        $validator = $this->resolver->resolveObject($validatorName);
        if ($validator instanceof IValidator) {
            return $validator;
        }

        throw new Exception("Validator: $validatorName is not a valid validator");
    }
}