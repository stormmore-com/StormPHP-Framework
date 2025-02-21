<?php

use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;
use Stormmore\Framework\AppConfiguration;
use \Stormmore\Framework\Internationalization\Locale;

#[Controller]
readonly class StatusController
{
    public function __construct(private AppConfiguration $configuration)
    {

    }
    #[Route("/status")]
    public function index(): View
    {
        return view("@src/templates/status", ['env' => $this->configuration->environment]);
    }
}