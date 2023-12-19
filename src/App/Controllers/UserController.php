<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\{UserService};

readonly class UserController
{
    public function __construct(
        private UserService $userService
    ) {
    }

    public function getAll(): void
    {
        echo json_encode([
            'message' => 'users',
            'data' => $this->userService->getAllUsers()
        ]);

    }

    public function create(): void
    {
        $params = json_decode(file_get_contents('php://input'), true);
        $this->userService->create($params['user']);
    }

    public function update(): void
    {

    }

}