<?php

namespace User;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\Authentication\Authenticate;
use Stormmore\Framework\Mvc\Authentication\Authorize;
use Stormmore\Framework\Mvc\View\View;

#[Controller()]
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