<?php

declare(strict_types=1);

namespace App\Handler\Ticket;


use App\Request;
use App\Response;
use App\ZammadClient;
use ZammadAPIClient\ResourceType;


class TicketState
{
    public function __invoke(Request $request, Response $response): void
    {

        $pendingTime = '2023-12-01 12:00:00'; // Установленное время напоминания в формате 'YYYY-MM-DD HH:MM:SS'


        $client = (new ZammadClient())->getClient();
        $params = json_decode(file_get_contents('php://input'), true);

        $ticket = $client->resource(ResourceType::TICKET)->get($params['ticketId']);
        
        $ticket->setValue('state', $params['stateName']);
        $ticket->setValue('pending_time', $pendingTime);
        $ticket->save();

        (new Response())->success()->send();
    }

}