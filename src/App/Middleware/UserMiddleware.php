<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\JWTCodecService;
use App\Services\UserService;
use Framework\Contracts\MiddlewareInterface;

class UserMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly JWTCodecService $codec, private readonly UserService $userService)
    {
    }
    public function process(callable $next)
    {
        try {
            if (preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
                $data = $this->codec->decode($matches[1]);
                $user_id = $data['sub'];
                $user = $this->userService->getById($user_id);
                $this->userService->setUser($user);
            }
        } catch (\Exception) {

        }
        $next();
    }
}