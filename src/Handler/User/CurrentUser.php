<?php

declare(strict_types=1);

namespace App\Handler\User;

use App\Request;
use App\Response;
use App\Session;

class CurrentUser
{
    public function __invoke(Request $request, Response $response): void
    {
        $session = new Session();

        $data = json_decode(file_get_contents('php://input'), true);
        $session->restoreSession($data['session_id']);

        if ($session->getData('user')) {
            (new Response())->success($session->getData('user'))->send();
            die;
        }
        (new Response())->error(message: 'Access denied', statusCode: 403)->send();
    }
}