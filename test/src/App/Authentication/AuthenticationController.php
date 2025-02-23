<?php

namespace Authentication;

use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;

#[Controller]
class AuthenticationController
{
    public function __construct()
    {
    }

    #[Route("/signin")]
    public function signIn(): View
    {
        return view("@templates/authentication/signin");
    }
}