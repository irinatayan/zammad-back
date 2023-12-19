<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;

class ErrorController
{
    public function __construct(private TemplateEngine $view)
    {
    }

    public function notFound(): void
    {
        http_response_code(404);
        echo json_encode([
            'message' => 'not found'
        ]);
    }
}