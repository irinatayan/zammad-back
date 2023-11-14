<?php

declare(strict_types=1);

namespace App;

use ZammadAPIClient\Client;

class ZammadClient
{
    private Client $client;
    public function __construct() {

        $this->client = new Client([
            'url'           => 'https://test-zammad-dev.zammad.com/', // URL to your Zammad installation
            'username'      => 'REDACTED_EMAIL',  // Username to use for authentication
            'password'      => 'REDACTED',// Password to use for authentication
        ]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}