<?php

namespace App\Services;
use App\Exceptions\InvalidSignatureException;
use App\Exceptions\TokenExpiredException;
use Exception;

class AuthService
{
    public function __construct(
        private JWTCodecService $codec,
        private UserService $userService
    )
    {
    }

    public function authenticateAccessToken(): bool
    {
        if ( ! preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
            http_response_code(400);
            echo json_encode(["message" => "incomplete authorization header"]);
            return false;
        }

        try {
            $data = $this->codec->decode($matches[1]);
        } catch (InvalidSignatureException) {
            http_response_code(400);
            echo json_encode(["message" => 'invalid signature ']);
            return false;
        } catch (TokenExpiredException) {
            http_response_code(401);
            echo json_encode(["message" => "token has expired"]);
            return false;
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["message" => $e->getMessage()]);
            return false;
        }

        $this->userService->setUser($this->userService->getById($data['sub']));
        return true;
    }
}