<?php

namespace src\App;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\View\View;

#[Controller]
readonly class HomepageController
{
    #[Route("/")]
    public function index(): View
    {
        return view("@templates/homepage");
    }
}