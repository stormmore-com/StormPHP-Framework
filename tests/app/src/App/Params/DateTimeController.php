<?php

namespace src\App\Params;

use DateTime;
use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class DateTimeController
{
    #[Route("/params/date-time")]
    public function required(DateTime $date)
    {
        return $date->format("Y-m-d H:i:s");
    }

    #[Route("/params/date-time-default")]
    public function def(DateTime $date = new DateTime("07-07-1997"))
    {
        return $date->format("Y-m-d H:i:s");
    }

    #[Route("/params/date-time-optional")]
    public function optional(?DateTime $date)
    {
        return $date?->format("Y-m-d H:i:s") ?? 'NULL';
    }
}