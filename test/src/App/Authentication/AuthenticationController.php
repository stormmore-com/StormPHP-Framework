<?php

namespace Authentication;

use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;
use Stormmore\Framework\Request\Redirect;
use Stormmore\Framework\Request\Request;

#[Controller]
readonly class AuthenticationController
{
    public function __construct(private Request $request, private AuthenticationService $authenticationService)
    {
    }

    #[Route("/signin")]
    public function signin(): View|Redirect
    {
        if ($this->request->isPost()) {
            $username = $this->request->getParameter('username');
            $this->authenticationService->signin($username);
            return redirect();
        }
        return view("@templates/authentication/signin");
    }

    #[Route('/signout')]
    public function signout(): Redirect
    {
        $this->authenticationService->signout();
        return redirect();
    }
}