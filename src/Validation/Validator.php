<?php

namespace Stormmore\Framework\Validation;

class Validator
{
    private array $fields;

    function __construct()
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
                $validatorResult = $validator->validate($value, $name, array(), []);
                if (!$validatorResult->isValid) {
                    $result->addError($name, $validatorResult->message);
                }
            }
        }
        return $result;
    }
}