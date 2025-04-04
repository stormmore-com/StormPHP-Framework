<?php

namespace src\App\Params;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class ArrayController
{
    #[Route("/params/array")]
    public function required(array $array)
    {
        return "array: [" . implode(',', $array) . "]";
    }

    #[Route("/params/array-default")]
    public function def(array $array = [1,2,3,4,5])
    {
        return "array: [" . implode(',', $array) . "]";
    }

    #[Route("/params/array-optional")]
    public function optional(?array $array)
    {
        if ($array === null) {
            return "array is null";
        }
        return "array: [" . implode(',', $array) . "]";
    }
    #[Route("/params/array-optional-default")]
    public function optionalDefault(?array $array = [1,23])
    {
        if ($array === null) {
            return "array is null";
        }
        return "array: [" . implode(',', $array) . "]";
    }
}