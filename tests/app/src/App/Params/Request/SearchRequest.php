<?php

namespace src\App\Params\Request;


use Stormmore\Framework\Mvc\Attributes\Bindable;

#[Bindable]
class SearchRequest
{
    public string $phrase;

    public int $limit = 10;

    public int $offset;

    public string $order = "asc";
}