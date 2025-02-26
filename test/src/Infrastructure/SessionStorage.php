<?php

namespace Infrastructure;

use Stormmore\Framework\Request\Cookie;
use Stormmore\Framework\Request\Response;
use stdClass;
use DateTime;

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
        $this->response->cookies->set(new Cookie('session', $json));
    }

    public function delete(): void
    {
        $this->response->cookies->delete('session');
    }
}