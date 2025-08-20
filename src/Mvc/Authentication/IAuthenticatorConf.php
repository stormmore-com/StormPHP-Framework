<?php

namespace Stormmore\Framework\Mvc\Authentication;

interface IAuthenticatorConf
{
    public function authenticate(AppUser $appUser);
}