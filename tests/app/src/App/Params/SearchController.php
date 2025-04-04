<?php

namespace src\App\Params;

use DateTime;
use src\App\Params\Request\SearchRequest;
use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class SearchController
{

    #[Route("/params/search-required")]
    public function required(string $phrase, string $order, DateTime $from, DateTime $to)
    {
        return "phrase: `$phrase`, order: `$order`, from: `{$from?->format('Y-m-d')}`, to: `{$to?->format('Y-m-d')}`";
    }

    #[Route("/params/search")]
    public function def(?string $phrase, ?string $order, ?DateTime $from, ?DateTime $to)
    {
        return "phrase: `$phrase`, order: `$order`, from: `{$from?->format('Y-m-d')}`, to: `{$to?->format('Y-m-d')}`";
    }


    #[Route("/params/search-optional")]
    public function optional(string $phrase = "today news",
                             string $order = "asc",
                             DateTime $from = new DateTime("yesterday"),
                             DateTime $to = new DateTime('today'))
    {
        return "phrase: `$phrase`, order: `$order`, from: `{$from?->format('Y-m-d')}`, to: `{$to?->format('Y-m-d')}`";
    }

    #[Route("/params/search-object")]
    public function searchObject(SearchRequest $searchRequest)
    {
        var_export($searchRequest);
        die;
    }
}