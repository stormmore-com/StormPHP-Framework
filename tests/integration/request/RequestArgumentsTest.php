<?php

namespace integration\request;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Cli\CliArguments;
use Stormmore\Framework\Http\FormData;

class RequestArgumentsTest extends TestCase
{
    public function testPostArguments(): void
    {
        $_SERVER['argv'] = [
            '',
            '-form',
            (new FormData())
                ->add('field1', 'value1')
                ->add('tab[]', 'tab_flat_1')
                ->add('tab[]', 'tab_flat_2')
                ->add('tab2["a"][0]', '1tab[][]')
                ->add('tab2["a"][1]', '2tab[][]')
        ];
        $requestArguments = new CliArguments();

        $this->assertEquals([
            'field1' => 'value1',
            'tab' => ['tab_flat_1', 'tab_flat_2'],
            'tab2' => [
                'a' => [
                    '1tab[][]',
                    '2tab[][]'
                ],
            ],
        ], $requestArguments->getPostParameters());
    }
}