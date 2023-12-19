<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;
use App\Services\UserService;
use Framework\Contracts\MiddlewareInterface;

class AdminRequiredMiddleware implements MiddlewareInterface
{
    public function __construct(private UserService $userService)
    {
    }
    public function process(callable $next)
    {
        $user = $this->userService->getUser();
        if ($user['role'] === "user") {
            http_response_code(403);
            echo json_encode(["message" => 'access denied ']);
            exit();
        }
        $next();
    }
}