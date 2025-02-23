<?php

use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;

#[Controller]
readonly class HomepageController
{
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
}