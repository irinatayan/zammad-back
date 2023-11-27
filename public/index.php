<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Handler\OptionsHeaders;
use App\Handler\Ticket\StatesPrioritiesAgents;
use App\Handler\Ticket\Ticket;
use App\Handler\Ticket\Tickets;
use App\Handler\Ticket\UpdateTicketsOwner;
use App\Handler\Ticket\TicketSearch;
use App\Handler\Ticket\Update as TicketUpdate;
use App\Handler\Ticket\TicketState;
use App\Handler\User\Create as UserCreate;
use App\Handler\User\Login as UserLogin;
use App\Handler\User\Update as UserUpdate;
use App\Handler\User\Delete as UserDelete;
use App\Handler\User\CurrentUser;
use App\Handler\User\Logout;
use App\Handler\User\Users;
use App\Router;
use App\Handler\Ticket\TicketPriority;

$router = new Router();

$router->post('/backend/user/create', callback: UserCreate::class);
$router->options('/backend/user/create', callback: OptionsHeaders::class);

$router->post('/backend/user/update', callback: UserUpdate::class);
$router->options('/backend/user/update', callback: OptionsHeaders::class);

$router->post('/backend/user/delete', callback: UserDelete::class);
$router->options('/backend/user/delete', callback: OptionsHeaders::class);

$router->post('/backend/user/login', callback: UserLogin::class);
$router->options('/backend/user/login', callback: OptionsHeaders::class);
$router->post('/backend/user/current', callback: CurrentUser::class);
$router->options('/backend/user/current', callback: OptionsHeaders::class);
$router->post('/backend/user/logout', callback: Logout::class);
$router->options('/backend/user/logout', callback: OptionsHeaders::class);
$router->get('/backend/users', callback: Users::class);
$router->options('/backend/users', callback: OptionsHeaders::class);


$router->get('/backend/tickets', callback: Tickets::class);
$router->options('/backend/tickets', callback: OptionsHeaders::class);
$router->get('/backend/ticket', callback: Ticket::class);
$router->options('/backend/ticket', callback: OptionsHeaders::class);
$router->get('/backend/search/ticket', callback: TicketSearch::class);
$router->options('/backend/search/ticket', callback: OptionsHeaders::class);
$router->post('/backend/ticket', callback: TicketUpdate::class);
$router->options('/backend/ticket', callback: OptionsHeaders::class);
$router->post('/backend/ticket/priority', callback: TicketPriority::class);
$router->options('/backend/ticket/priority', callback: OptionsHeaders::class);
$router->post('/backend/ticket/state', callback: TicketState::class);
$router->options('/backend/ticket/state', callback: OptionsHeaders::class);
$router->get('/backend/ticket/bulk-update-info', callback: StatesPrioritiesAgents::class);
$router->options('/backend/ticket/bulk-update-info', callback: OptionsHeaders::class);

$router->post('/backend/tickets/update/owner', callback: UpdateTicketsOwner::class);
$router->options('/backend/tickets/update/owner', callback: OptionsHeaders::class);



$router->run($_SERVER);