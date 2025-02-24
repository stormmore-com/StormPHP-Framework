<?php

namespace Infrastructure;

use Stormmore\Framework\Request\Response;
use stdClass;
use DateTime;

readonly class SessionStorage
{
    public function __construct(private Response $response)
    {
    }

    public function save(string $username): void
    {
        $now = new DateTime();
        $session = new stdClass();
        $session->username = $username;
        $session->createdAt = $now->format("Y-m-d H:i:s");
        $json = json_encode($session);
        $this->response->setCookie('session', $json);
    }
}