<?php

namespace Stormmore\Framework\Http;

class Field
{
    private string $name;
    private bool $isArrayType;
    private array $arrayPath;

    public function __construct(private string $field, private mixed $value)
    {
        $this->name = $this->field = trim($this->field);
        $this->isArrayType = false;
        $this->separateNameAndArrayChain();
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function isArrayType(): bool
    {
        return $this->isArrayType;
    }

    public function getArrayPath(): array
    {
        return array_merge(["[$this->name]"], $this->arrayPath);
    }

    private function separateNameAndArrayChain(): void
    {
        if (str_contains($this->field, '[')) {
            $this->isArrayType = true;
            $this->name = substr($this->field, 0, strpos($this->field, '['));

            $offset = 0;
            while(true) {
                $openingBracketPos = strpos($this->field, '[', $offset);
                $closingBracketPos = strpos($this->field, ']', $offset);

                if ($openingBracketPos === false and $closingBracketPos === false) {
                    break;
                }

                if ($openingBracketPos === false or $closingBracketPos === false) {
                    $this->isArrayType = false;
                    break;
                }

                if ($openingBracketPos > $closingBracketPos) {
                    $this->isArrayType = false;
                    break;
                }

                $path = substr($this->field, $openingBracketPos, $closingBracketPos - $openingBracketPos + 1);
                $this->arrayPath[] = $path;

                $offset = $closingBracketPos + 1;
            }
        }
    }
}