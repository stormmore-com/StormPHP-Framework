<?php

namespace Authentication;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\Request\Redirect;
use Stormmore\Framework\Mvc\Request\Request;
use Stormmore\Framework\Mvc\View\View;

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
            $privileges = $this->request->getParameter('privileges', []);
            $username = $this->request->getParameter('username');
            $this->authenticationService->signin($username, $privileges);
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