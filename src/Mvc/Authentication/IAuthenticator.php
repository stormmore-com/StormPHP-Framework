<?php

namespace Stormmore\Framework\Mvc\Authentication;

interface IAuthenticator
{
    public function authenticate(AppUser $appUser);
}