<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\TokenExpiredException;
use App\Services\JWTCodecService;
use App\Services\UserService;
use Framework\Contracts\MiddlewareInterface;

readonly class UserMiddleware implements MiddlewareInterface
{
    public function __construct(private JWTCodecService $codec, private UserService $userService)
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
        }
        catch (TokenExpiredException ) {
//            http_response_code(401);
//            echo json_encode(["message" => "token has expired"]);

        } catch (\Exception) {
            $this->userService->setUser(null);
        }
        $next();
    }
}