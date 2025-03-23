<?php

namespace Params;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class ParamsController
{
    #[Route('/params')]
    public function index()
    {
        return View('@templates/params/index');
    }
}