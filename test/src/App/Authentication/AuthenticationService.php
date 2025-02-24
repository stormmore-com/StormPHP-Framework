<?php

namespace Authentication;

use Infrastructure\SessionStorage;

readonly class AuthenticationService
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function signin(string $username): void
    {
        $this->storage->save($username);
    }

    public function signout(): void
    {
        $this->storage->delete();
    }
}