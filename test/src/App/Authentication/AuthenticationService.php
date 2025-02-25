<?php

namespace Authentication;

use Infrastructure\SessionStorage;

readonly class AuthenticationService
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function signin(string $username, array $privileges): void
    {
        $this->storage->save($username, $privileges);
    }

    public function signout(): void
    {
        $this->storage->delete();
    }
}