<?php

declare(strict_types=1);

namespace App\Handler\User;

use App\Request;
use App\Response;
use App\Database;

class Delete
{
    public function __invoke(Request $request, Response $response): void
    {
        try {
            $config = include_once __DIR__ . '/../../../config/database.php';
            $database = new Database($config['dsn'], $config['username'], $config['password']);

            $userData = json_decode(file_get_contents('php://input'), true);
            $userId = $userData['id'];

            $checkUserStmt = $database->getConnection()->prepare("SELECT COUNT(*) FROM user WHERE id = :id");
            $checkUserStmt->bindParam(':id', $userId, $database->getConnection()::PARAM_INT);
            $checkUserStmt->execute();
            $userExists = $checkUserStmt->fetchColumn();

            if ($userExists > 0) {
                $deleteStmt = $database->getConnection()->prepare("DELETE FROM user WHERE id = :id");
                $deleteStmt->bindParam(':id', $userId, $database->getConnection()::PARAM_INT);
                $deleteStmt->execute();

                (new Response())->success(message: 'User successfully deleted')->send();
            } else {
                // User with the given ID does not exist
                (new Response())->error(message: "User with ID {$userId} not found", statusCode: 404)->send();
            }

        } catch (\Exception $e) {
            (new Response())->error(message: $e->getMessage(), statusCode: 500)->send();
        } catch (\Error $e) {
            (new Response())->error(message: $e->getMessage(), statusCode: 500)->send();
        }
    }
}
