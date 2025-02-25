<?php

namespace User;

use Stormmore\Framework\Authentication\Authenticate;
use Stormmore\Framework\Authentication\Authorize;
use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;

#[Controller]
class UserController
{
    #[Authenticate]
    #[Route("/profile")]
    public function profile(): View
    {
        return view('@templates/user/profile');
    }

    #[Authenticate]
    #[Authorize("administrator")]
    #[Route("/administrator")]
    public function admin(): View
    {
        return view('@templates/user/administrator');
    }
}