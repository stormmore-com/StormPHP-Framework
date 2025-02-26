<?php

use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;
use Stormmore\Framework\Request\Redirect;
use Stormmore\Framework\Request\Response;

#[Controller]
readonly class HomepageController
{
    public function __construct(private Response $response)
    {
    }

    #[Route("/")]
    public function index(): View
    {
        return view("@templates/homepage");
    }

    #[Route("/url-made-only-to-throw-excception-but-it-exist")]
    public function exceptionEndpoint()
    {
        throw new Exception("Plain exception without meaningful message. Day as always.");
    }

    #[Route("/redirect-with-success")]
    public function redirectWithSuccess(): Redirect
    {
        $this->response->messages->add("success");
        return redirect();
    }

    #[Route("/redirect-with-failure")]
    public function redirectWithFailure(): Redirect
    {
        $this->response->messages->add("failure");
        return redirect();
    }
}