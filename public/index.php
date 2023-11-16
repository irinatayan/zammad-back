<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Handler\OptionsHeaders;
use App\Handler\Ticket\Ticket;
use App\Handler\Ticket\Tickets;
use App\Handler\Ticket\TicketSearch;
use App\Handler\Ticket\Update as TicketUpdate;
use App\Handler\User\Create as UserCreate;
use App\Handler\User\Login as UserLogin;
use App\Handler\User\CurrentUser;
use App\Handler\User\Logout;
use App\Router;
use App\Handler\Ticket\TicketPriority;

$router = new Router();

$router->post('/backend/user/create', callback: UserCreate::class);
$router->options('/backend/user/create', callback: OptionsHeaders::class);
$router->post('/backend/user/login', callback: UserLogin::class);
$router->options('/backend/user/login', callback: OptionsHeaders::class);
$router->post('/backend/user/current', callback: CurrentUser::class);
$router->options('/backend/user/current', callback: OptionsHeaders::class);
$router->post('/backend/user/logout', callback: Logout::class);
$router->options('/backend/user/logout', callback: OptionsHeaders::class);
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



$router->run($_SERVER);