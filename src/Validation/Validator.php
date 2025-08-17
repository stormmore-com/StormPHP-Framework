<?php

namespace Stormmore\Framework\Validation;

use Exception;
use closure;
use stdClass;

class Validator
{
    private ?ValidationResult $result = null;
    private array $fields;

    function __construct()
    {
        $this->fields = array();
    }

    public static function create(): Validator
    {
        return new Validator();
    }

    public function field(string $field, closure $closure): Validator
    {
        $field = new Field($field);
        $closure->__invoke($field);
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    public function add(Field $field): Validator
    {
        $this->fields[$field->getName()] = $field;
        return $this;
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

    public function getResult(): ?ValidationResult
    {
        return $this->result;
    }

    public function isValid(object $object = new stdClass()): bool
    {
        $this->result = $this->validate($object);
        return $this->result->isValid();
    }

    public function validate(object $data): ValidationResult
    {
        $result = new ValidationResult();
        foreach ($this->fields as $field) {
            $name = $field->getName();
            $value = $data->{$name};
            if ($value == null) {
                $value = $field->getValue();
            }
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