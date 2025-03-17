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

    public function alpha(null|string $message = null): Field
    {
        $this->validators[] = new AlphaValidator($message);
        return $this;
    }

    public function alphaNumeric(null|string $message = null): Field
    {
        $this->validators[] = new AlphaNumValidator($message);
        return $this;
    }

    public function email(null|string $message = null): Field
    {
        $this->validators[] = new EmailValidator($message);
        return $this;
    }

    public function float(null|string $message = null): Field
    {
        $this->validators[] = new FloatValidator($message);
        return $this;
    }

    /**
     * @param array $types list of accepted constants https://www.php.net/manual/en/function.exif-imagetype.php
     * @return $this
     */
    public function image(array $types = [], null|string $message = null): Field
    {
        $this->validators[] = new ImageValidator($types, $message);
        return $this;
    }

    public function file(array $extensions = array(), int $size = 0, null|string $message = null): Field
    {
        $this->validators[] = new FileValidator($extensions, $size, $message);
        return $this;
    }

    public function int(null|string $message = null): Field
    {
        $this->validators[] = new IntValidator($message);
        return $this;
    }

    public function max(int $max, null|string $message = null): Field
    {
        $this->validators[] = new MaxValidator($max, $message);
        return $this;
    }

    public function min(int $min, null|string $message = null): Field
    {
        $this->validators[] = new MinValidator($min, $message);
        return $this;
    }

    public function number(null|string $message = null): Field
    {
        $this->validators[] = new NumberValidator($message);
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

    public function values(array $values, null|string $message = null): field
    {
        $this->validators[] = new ValuesValidator($values, $message);
        return $this;
    }

    public function regexp(string $regexp, null|string $message = null): Field
    {
        $this->validators[] = new RegexpValidator($regexp, $message);
        return $this;
    }

    public function required(null|string $message = null): Field
    {
        $this->validators[] = new RequiredValidator($message);
        return $this;
    }

    public function validator(IValidator $validator): Field
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return IValidator[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }
}