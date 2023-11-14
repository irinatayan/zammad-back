<?php

declare(strict_types=1);

namespace App\Handler\User;

use App\Authorization;
use App\AuthorizationException;
use App\Request;
use App\Response;
use App\Database;
use App\Session;

class Logout
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = $request->getParams();

        $session = new Session();
        $data = json_decode(file_get_contents('php://input'), true);
        $session->restoreSession($data['session_id']);

        $config = include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
        $database = new Database($config['dsn'], $config['username'], $config['password']);
        $authorization = new Authorization($database, $session);

        $authorization->logout();
        $session->save();
    }
}