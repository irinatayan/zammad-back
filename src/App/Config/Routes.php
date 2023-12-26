<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\{HomeController,
    AboutController,
    AuthController,
    HookController,
    OptionsController,
    TicketController,
    TransactionController,
    ErrorController,
    UserController};
use App\Middleware\{AdminRequiredMiddleware, AuthRequiredMiddleware, GuestOnlyMiddleware};

function registerRoutes(App $app): void
{
    $app->post('/backend/login', [AuthController::class, 'login']);
    $app->post('/backend/refresh', [AuthController::class, 'refreshToken']);
    $app->post('/register', [AuthController::class, 'register']);

    $app->get('/backend/ticket/search', [TicketController::class, 'search'])->add(AdminRequiredMiddleware::class)->add(AuthRequiredMiddleware::class);
    $app->get('/backend/tickets', [TicketController::class, 'getAll'])->add(AdminRequiredMiddleware::class)->add(AuthRequiredMiddleware::class);
    $app->get('/backend/ticket', [TicketController::class, 'getTicket'])->add(AuthRequiredMiddleware::class);
    $app->post('/backend/ticket/owner', [TicketController::class, 'updateTicketOwner'])->add(AuthRequiredMiddleware::class);
    $app->post('/backend/ticket/priority', [TicketController::class, 'updateTicketPriority'])->add(AuthRequiredMiddleware::class);
    $app->post('/backend/ticket/state', [TicketController::class, 'updateTicketState'])->add(AuthRequiredMiddleware::class);
    $app->get('/backend/tickets/bulk-update-info', [TicketController::class, 'statesPrioritiesAgents'])->add(AuthRequiredMiddleware::class);
    $app->post('/backend/tickets/owner', [TicketController::class, 'updateTicketsOwner'])->add(AuthRequiredMiddleware::class);
    $app->post('/backend/tickets/priority', [TicketController::class, 'updateTicketsPriority'])->add(AuthRequiredMiddleware::class);
    $app->post('/backend/tickets/state', [TicketController::class, 'updateTicketsState'])->add(AuthRequiredMiddleware::class);

    $app->get('/backend/users', [UserController::class, 'getAll'])->add(AdminRequiredMiddleware::class)->add(AuthRequiredMiddleware::class);
    $app->post('/backend/users/create', [UserController::class, 'create'])->add(AdminRequiredMiddleware::class)->add(AuthRequiredMiddleware::class);

    $app->get('/logout', [AuthController::class, 'logout'])->add(AuthRequiredMiddleware::class);

    $app->post('/backend/test', [HookController::class, 'sendNote']);
    $app->get('/backend/voice', [HookController::class, 'getVoiceAttachment']);



    $app->setErrorHandler([ErrorController::class, 'notFound']);
    $app->setOptionsHandler([OptionsController::class, 'sendAllowOrigin']);
}