<?php

namespace src;

use Stormmore\Framework\Controller\Controller;
use Stormmore\Framework\Controller\Route;
use Stormmore\Framework\Template\View;

#[Controller]
class TestController
{
    #[Route("/test")]
    public function index(): View
    {
        return view("@src/test_template.php");
    }
}