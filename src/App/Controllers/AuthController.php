<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Response;
use App\Services\{JWTCodecService, ValidatorService, UserService};

readonly class AuthController
{
    public function __construct(
        private ValidatorService $validatorService,
        private UserService $userService
    ) {
    }

    public function register(): void
    {
        (new Response())->success([])->send();
//        $this->validatorService->validateRegister($_POST);
//        $this->userService->isEmailTaken($_POST['email']);
//        $this->userService->create($_POST);
//
//        redirectTo('/');
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->validatorService->validateLogin($data);
        $this->userService->login($data);

    }

    public function logout(): void
    {
        $this->userService->logout();
        redirectTo('/login');
    }

    public function refreshToken(): void
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $this->userService->refreshToken($data);
    }

    public function currentUser(): void
    {
        echo json_encode([
            'data' => [
                "user" => $this->userService->getUser()
            ]
        ]);
    }
}