<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\{HomeController,
    AboutController,
    AuthController,
    OptionsController,
    TicketController,
    TransactionController,
    ErrorController};
use App\Middleware\{AuthRequiredMiddleware, GuestOnlyMiddleware};

function registerRoutes(App $app): void
{
    $app->post('/backend/login', [AuthController::class, 'login']);
    $app->post('/backend/refresh', [AuthController::class, 'refreshToken']);
    $app->post('/backend/me', [AuthController::class, 'currentUser']);

    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/backend/ticket/search', [TicketController::class, 'search'])->add(AuthRequiredMiddleware::class);
    $app->get('/logout', [AuthController::class, 'logout'])->add(AuthRequiredMiddleware::class);

    $app->get('/transaction', [TransactionController::class, 'createView'])->add(AuthRequiredMiddleware::class);
    $app->post('/transaction', [TransactionController::class, 'create'])->add(AuthRequiredMiddleware::class);
    $app->get('/transaction/{transaction}', [TransactionController::class, 'editView'])->add(AuthRequiredMiddleware::class);
    $app->post('/transaction/{transaction}', [TransactionController::class, 'update'])->add(AuthRequiredMiddleware::class);
    $app->delete('/transaction/{transaction}', [TransactionController::class, 'delete'])->add(AuthRequiredMiddleware::class);

    $app->setErrorHandler([ErrorController::class, 'notFound']);
    $app->setOptionsHandler([OptionsController::class, 'sendAllowOrigin']);
}