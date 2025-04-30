<?php

namespace src\Infrastructure;

use DateTime;
use stdClass;
use Stormmore\Framework\Mvc\IO\Cookie\Cookie;
use Stormmore\Framework\Mvc\IO\Response;

readonly class SessionStorage
{
    public function __construct(private Response $response)
    {
    }

    public function save(string $username, array $privileges): void
    {
        $now = new DateTime();
        $session = new stdClass();
        $session->username = $username;
        $session->createdAt = $now->format("Y-m-d H:i:s");
        $session->privileges = $privileges;
        $json = json_encode($session);
        $this->response->setCookie(new Cookie('session', $json));
    }

    public function delete(): void
    {
        $this->response->unsetCookie('session');
    }
}