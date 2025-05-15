<?php

namespace Client;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Http\Cookie;
use Stormmore\Framework\Http\FormData;
use Stormmore\Framework\Http\Header;
use Stormmore\Framework\Tests\Client\AppResponse;

class CliClientTest extends TestCase
{
    public function testGetRequest(): void
    {
        $command = "-r /test/get -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPostRequest(): void
    {
        $command = "-r /test/post -method post -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPutRequest(): void
    {
        $command = "-r /test/put -method put -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPatchRequest(): void
    {
        $command = "-r /test/patch -method patch -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testDeleteRequest(): void
    {
        $command = "-r /test/delete -method delete -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    /**
     * TODO
     */
    public function testQueryParameters(): void
    {
        $command = "-r /test/concatenate-query-params -parameters a=1 b=2 c=3 -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("123", $response->getBody());
    }

    public function testSendingHeader(): void
    {
        $command = "-r /test/get-header -headers service-key:123abc321 -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("123abc321", $response->getBody());
    }

    public function testSendingCookie(): void
    {
        $command = "-r /test/write-cookie-to-body -cookies session-id:54321 service-key:123abc321 -print-headers";

        $response = $this->runCommand($command);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("54321", $response->getBody());
    }

    /**
     * TODO
     */
    public function tesPostFormWithFiles(): void
    {
        $response = $this->client
            ->request("POST", "/test/post/form")
            ->withForm((new FormData())
                ->add('prime[]', 1)
                ->add('prime[]', 2)
                ->add('number', 7)
                ->add('name', 'Micheal')
                ->addFile('file', $this->stormFilepath))
            ->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals((object)[
            'prime' => [1, 2],
            'number' => 7,
            'name' => 'Micheal',
            'file-md5' => '1648c2a85dd50f2dfaa51fb5c8478261'
        ], $response->getJson());
    }

    /**
     * TODO
     */
    public function tesCliCommand(): void
    {

    }

    /**
     * TODO
     */
    public function testSavingResult(): void
    {
        $file = getcwd() . DIRECTORY_SEPARATOR . "test.cli";
        $command = "-r /test/get > $file";

        $this->runCommand($command);

        $this->assertEquals("OK", $this->getTempFileContent($file));
    }

    private function getTempFileContent(string $filename): string
    {
        $content = file_get_contents($filename);
        unlink($filename);
        return $content;
    }

    private function runCommand(string $command): AppResponse
    {
        $cwd = getcwd();
        $public_html = dirname(__FILE__) . '/../../app/public_html';
        chdir($public_html);
        exec("php index.php $command", $output);
        chdir($cwd);

        return new AppResponse(implode("\n", $output));
    }
}