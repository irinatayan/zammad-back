<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Response;

class OptionsController
{
    public function __construct()
    {
    }

    public function sendAllowOrigin(): void
    {
        header('Vary: Origin');
        (new Response())->success([])->send();
    }
}