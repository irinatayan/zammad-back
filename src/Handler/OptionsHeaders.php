<?php

declare(strict_types=1);

namespace App\Handler;

use App\Request;
use App\Response;

class OptionsHeaders
{
    public function __invoke(Request $request, Response $response): void
    {
        header('Vary: Origin');
        (new Response())->success([])->send();
    }
}