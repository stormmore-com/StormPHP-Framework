<?php

namespace integration;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Mvc\IO\Request\RequestArguments;

class RequestArgumentsTest extends TestCase
{

    public function testPostArguments(): void
    {
        $_SERVER['argv'] = [
            '',
            '-form',
            [
               'field1' => 'value1',
               'tab[]' => 'tab_flat_1',
               'tab[]' => 'tab_flat_2',
               'tab2[][][]' => 'tab2_1',
               'tab2[]["test"][]' => 'tab2_test',
            ]
        ];
        $requestArguments = new RequestArguments();

        echo var_export($requestArguments->getPostParameters(), true);die;
        $this->assertEquals([
            'field1' => 'value1',
            'tab' => ['tab_flat_1', 'tab_flat_2'],
            'tab2' => [
                [
                    ['tab2_1'],
                    'test' => ['tab2_test']
                ],
            ],
        ], $requestArguments->getPostParameters());
    }
}