<?php

declare(strict_types=1);

namespace App\Handler\User;

use App\Request;
use App\Response;
use App\Database;
use PDOException;

class Update
{
    public function __invoke(Request $request, Response $response): void
    {
        $users = $this->getAgents();
        (new Response())->success("data")->send();

    }

    public function getAgents()
    {
        try {
            $config = include_once __DIR__ . '/../../../config/database.php';
            $database = new Database($config['dsn'], $config['username'], $config['password']);

            $userData = json_decode(file_get_contents('php://input'), true);

            $userId = $userData['id'];
            $newUsername = $userData['user']['username'];
            $newEmail = $userData['user']['email'];
            $newPassword = $userData['user']['password'];

            $checkEmailStmt = $database->getConnection()->prepare("SELECT COUNT(*) FROM user WHERE email = :email AND id <> :id");
            $checkEmailStmt->bindParam(':email', $newEmail);
            $checkEmailStmt->bindParam(':id', $userId, $database->getConnection()::PARAM_INT);
            $checkEmailStmt->execute();
            $emailExists = $checkEmailStmt->fetchColumn();

            if ($emailExists > 0) {
                (new Response())->error(message: "User with this email already exists", statusCode: 500)->send();
            } else {
                if (!empty($newPassword)) {
                    $newPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    $updateStmt = $database->getConnection()->prepare("UPDATE user SET username = :username, email = :email, password = :password WHERE id = :id");
                    $updateStmt->bindParam(':password', $newPassword);
                } else {
                    $updateStmt = $database->getConnection()->prepare("UPDATE user SET username = :username, email = :email WHERE id = :id");
                }

                $updateStmt->bindParam(':id', $userId, $database->getConnection()::PARAM_INT);
                $updateStmt->bindParam(':username', $newUsername);
                $updateStmt->bindParam(':email', $newEmail);

                $updateStmt->execute();

                echo "Данные пользователя успешно обновлены.";
            }

        } catch (\Exception $e) {
            (new Response())->error(message: $e->getMessage(), statusCode: 500)->send();
        } catch (\Error $e) {
            (new Response())->error(message: $e->getMessage(), statusCode: 500)->send();
        }

    }

}