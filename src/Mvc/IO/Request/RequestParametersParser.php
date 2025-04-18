<?php

namespace Stormmore\Framework\Mvc\IO\Request;

class RequestParametersParser
{
    public function __construct(private array $form)
    {
    }

    public function parse(): array
    {
        $parameters = [];
        foreach ($this->form as $field => $value) {
            [$name, $arraySegments] = $this->extractNameAndArraySegments($field);
            if (count($arraySegments)) {
                foreach($arraySegments as $arraySegment) {
                    if (!array_key_exists($name, $parameters)) {
                        $parameters[$name] = [];
                    }
                }
            }
            else {
                $parameters[$name] = $value;
            }
        }
        return $parameters;
    }

    private function extractNameAndArraySegments(string $field): array
    {
        $field = trim($field);
        $name = $field;
        $arraySegments = [];
        if (str_contains($field, '[')) {
            $name = substr($field, 0, strpos($field, '['));
            $segments = substr($field, strpos($field, '['));
            do {
                $end = strpos($segments, ']');
                $arraySegments[] = substr($segments, 0, $end + 1);
                $segments = substr($segments, $end + 1);
            }
            while(!empty($segments));
        }
        return [$name, $arraySegments];
    }

    private function buildPostArray(array &$array, string $segments)
    {
        $segments = trim($segments);
        $posB = strpos($segments, '[');
        $posE = strpos($segments, ']');

        $key = str_replace(['[', ']', "'", '"'], '', $segments);

        if ($key) {

        }

        $segments = substr($segments, $posE);
        if (!empty($segments))
        {

        }
    }
}