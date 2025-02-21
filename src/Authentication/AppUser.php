<?php

namespace Stormmore\Framework\Authentication;

class AppUser
{
    private bool $isAuthenticated = false;
    public bool $isAnonymous = true;
    public string $id;
    public string $name;
    public string $email;
    public array $data = [];
    public array $claims = [];

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function authenticate(): void
    {
        $this->isAuthenticated = true;
        $this->isAnonymous = false;
    }

    public function hasClaims(array $claims): bool
    {
        return count(array_intersect($this->claims, $claims)) == count($claims);
    }

    public function __get(string $key): mixed
    {
        return $this->data[$key];
    }

    public function __set(string $key, string $value): void
    {
        $this->data[$key] = $value;
    }
}