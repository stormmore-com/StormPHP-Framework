<?php

namespace Stormmore\Framework\Classes\Parser;

use PhpToken;

class PhpTokenBuffer
{
    private array $tokens = [];

    public function __construct(private int $size)
    {
    }

    public function add(string $token): void
    {
        $this->tokens[] = $token;
        if (count($this->tokens) > $this->size) {
            array_shift($this->tokens);
        }
    }

    public function get(int $index): ?string
    {
        if ($index > count($this->tokens) - 1) {
            return null;
        }
        return $this->tokens[$index];
    }

    public function getFromEnd(int $index = 0): ?string
    {
        return $this->tokens[count($this->tokens) - 1 - $index];
    }
}