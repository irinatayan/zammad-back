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
        (new Response())->success($users)->send();

    }

    public function getAgents()
    {
        try {
            $config = include_once __DIR__ . '/../../../config/database.php';
            $database = new Database($config['dsn'], $config['username'], $config['password']);

            $role = 'user';

            $statement = $database->getConnection()->prepare('SELECT id, username, email, role FROM user');
            $statement->execute();

            return $statement->fetchAll();

        } catch (\Exception $e) {
            (new Response())->error(message: $e->getMessage(), statusCode: 500)->send();
        } catch (\Error $e) {
            (new Response())->error(message: $e->getMessage(), statusCode: 500)->send();
        }

    }

}