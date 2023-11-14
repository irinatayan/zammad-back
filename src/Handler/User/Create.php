<?php

declare(strict_types=1);

namespace App\Handler\User;

use App\Authorization;
use App\AuthorizationException;
use App\Request;
use App\Response;
use App\Database;
use App\Session;

class Create
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = $request->getParams();

        $session = new Session();
        $config = include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
        $database = new Database($config['dsn'], $config['username'], $config['password']);
        $authorization = new Authorization($database, $session);

        $database->getConnection()->exec("
            CREATE TABLE IF NOT EXISTS user (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255),
                email VARCHAR(255),
                password  VARCHAR(100),
                role VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");

        try {
            $authorization->register($params);
            (new Response())->success(message: 'User created')->send();

        } catch (AuthorizationException $exception) {
            (new Response())->error(message: $exception->getMessage(), statusCode: 500)->send();

        }
    }
}