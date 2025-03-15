<?php

namespace Stormmore\Framework\Validation;

use Stormmore\Framework\Validation\Validators\AlphaNumValidator;
use Stormmore\Framework\Validation\Validators\AlphaValidator;
use Stormmore\Framework\Validation\Validators\EmailValidator;
use Stormmore\Framework\Validation\Validators\FileValidator;
use Stormmore\Framework\Validation\Validators\FloatValidator;
use Stormmore\Framework\Validation\Validators\ImageValidator;
use Stormmore\Framework\Validation\Validators\IntValidator;
use Stormmore\Framework\Validation\Validators\MaxValidator;
use Stormmore\Framework\Validation\Validators\MinValidator;
use Stormmore\Framework\Validation\Validators\NumberValidator;
use Stormmore\Framework\Validation\Validators\RegexpValidator;
use Stormmore\Framework\Validation\Validators\ValuesValidator;
use Stormmore\Framework\Validation\Validators\RequiredValidator;

class Field
{
    private array $validators;

    public function __construct(private readonly string $name)
    {
        $this->validators = array();
    }

    public function alpha(): Field
    {
        $this->validators[] = new AlphaValidator();
        return $this;
    }

    public function alphaNumeric(): Field
    {
        $this->validators[] = new AlphaNumValidator();
        return $this;
    }

    public function email(): Field
    {
        $this->validators[] = new EmailValidator();
        return $this;
    }

    public function float(): Field
    {
        $this->validators[] = new FloatValidator();
        return $this;
    }

    /**
     * @param array $types list of accepted constants https://www.php.net/manual/en/function.exif-imagetype.php
     * @return $this
     */
    public function image(array $types = []): Field
    {
        $this->validators[] = new ImageValidator($types);
        return $this;
    }

    public function file(array $extensions = array(), int $size = 0): Field
    {
        $this->validators[] = new FileValidator($extensions, $size);
        return $this;
    }

    public function int(): Field
    {
        $this->validators[] = new IntValidator();
        return $this;
    }

    public function max(int $max): Field
    {
        $this->validators[] = new MaxValidator($max);
        return $this;
    }

    public function min(int $min): Field
    {
        $this->validators[] = new MinValidator($min);
        return $this;
    }

    public function number(): Field
    {
        $this->validators[] = new NumberValidator();
        return $this;
    }

    public function value(mixed $value): field
    {
        if (!is_array($value)) {
            $value = array($value);
        }
        $this->validators[] = new ValuesValidator($value);
        return $this;
    }

    public function values(array $values): field
    {
        $this->validators[] = new ValuesValidator($values);
        return $this;
    }

    public function regexp(string $regexp): Field
    {
        $this->validators[] = new RegexpValidator($regexp);
        return $this;
    }

    public function required(): Field
    {
        $this->validators[] = new RequiredValidator();
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }
}