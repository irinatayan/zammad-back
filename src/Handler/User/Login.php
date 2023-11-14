<?php

declare(strict_types=1);

namespace App\Handler\User;

use App\Authorization;
use App\AuthorizationException;
use App\Request;
use App\Response;
use App\Database;
use App\Session;

class Login
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = $request->getParams();

        $session = new Session();
        $config = include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
        $database = new Database($config['dsn'], $config['username'], $config['password']);
        $authorization = new Authorization($database, $session);

        $session->start();

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            if ($authorization->login($data['email'], $data['password'])) {
                (new Response())->success(["session" => session_id(), "user" => $session->getData('user') ], message: session_id())->send();
                $session->save();
            }


        } catch (AuthorizationException $exception) {
            (new Response())->error(message: $exception->getMessage(), statusCode: 500)->send();
        }
    }
}