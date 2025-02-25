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
    public array $privileges = [];

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function authenticate(): void
    {
        $this->isAuthenticated = true;
        $this->isAnonymous = false;
    }

    public function setPrivileges(array $privileges): void
    {
        $this->privileges = $privileges;
    }

    public function hasPrivileges(array $claims): bool
    {
        return count(array_intersect($this->privileges, $claims)) == count($claims);
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