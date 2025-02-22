<?php

use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;

#[Controller]
class HomepageController
{
    #[Route("/")]
    public function index(): View
    {
        return view("@/src/templates/homepage", ['name' => 'John Doe']);
    }
}