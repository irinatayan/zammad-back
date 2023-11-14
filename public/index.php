<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Handler\OptionsHeaders;
use App\Handler\Ticket\Ticket;
use App\Handler\Ticket\Tickets;
use App\Handler\Ticket\TicketSearch;
use App\Handler\User\Create as UserCreate;
use App\Handler\User\Login as UserLogin;
use App\Handler\User\CurrentUser;
use App\Handler\User\Logout;
use App\Router;

$router = new Router();

$router->get('/user/create', callback: UserCreate::class);
$router->post('/user/login', callback: UserLogin::class);
$router->options('/user/login', callback: OptionsHeaders::class);
$router->post('/user/current', callback: CurrentUser::class);
$router->options('/user/current', callback: OptionsHeaders::class);
$router->post('/user/logout', callback: Logout::class);
$router->options('/user/logout', callback: OptionsHeaders::class);
$router->get('/tickets', callback: Tickets::class);
$router->options('/tickets', callback: OptionsHeaders::class);
$router->get('/ticket', callback: Ticket::class);
$router->options('/ticket', callback: OptionsHeaders::class);
$router->get('/search/ticket', callback: TicketSearch::class);
$router->options('/search/ticket', callback: OptionsHeaders::class);



$router->run($_SERVER);