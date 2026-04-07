<?php

declare(strict_types=1);

namespace App;

use ZammadAPIClient\Client;

class ZammadClient
{
    private Client $client;
    public function __construct() {

        $this->client = new Client([
            'url'           => $_ENV['ZAMMAD_URL'],
            'username'      => $_ENV['ZAMMAD_USERNAME'],
            'password'      => $_ENV['ZAMMAD_PASSWORD'],
        ]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}