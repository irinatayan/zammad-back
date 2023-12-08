<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;
use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class AuthRequiredMiddleware implements MiddlewareInterface
{
    public function __construct(private AuthService $auth)
    {
    }
    public function process(callable $next)
    {
        if (!$this->auth->authenticateAccessToken()) {
            exit();
        };
        $next();
    }
}