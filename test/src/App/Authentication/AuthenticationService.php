<?php

namespace Authentication;

use Infrastructure\SessionStorage;

class AuthenticationService
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function authenticate(string $username): void
    {
        $this->storage->save($username);
    }
}