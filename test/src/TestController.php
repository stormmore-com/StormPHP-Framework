<?php

namespace src;

use Stormmore\Framework\Controller\Controller;
use Stormmore\Framework\Controller\Route;

#[Controller]
class TestController
{
    #[Route("/view")]
    public function index()
    {
        return view("@src/test_template.php");
    }
}