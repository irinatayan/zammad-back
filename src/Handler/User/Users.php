<?php

declare(strict_types=1);

namespace App\Handler\User;

use App\Request;
use App\Response;
use App\Database;
use PDOException;

class Users
{
    public function __invoke(Request $request, Response $response): void
    {
            $users = $this->getAgents();
            $test = 20;
    }

    public function getAgents()
    {
        try {
            $config = include_once __DIR__ . '/../../../config/database.php';
            $database = new Database($config['dsn'], $config['username'], $config['password']);

            $role = 'user';

            $statement = $database->getConnection()->prepare('SELECT * FROM user WHERE role = :role');
            $statement->execute([
                'email' => $role,
            ]);

            return $statement->fetch();

        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }

    }

}