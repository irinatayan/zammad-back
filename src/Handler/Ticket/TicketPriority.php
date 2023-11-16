<?php

declare(strict_types=1);

namespace App\Handler\Ticket;


use App\Request;
use App\Response;
use App\ZammadClient;
use ZammadAPIClient\ResourceType;


class TicketPriority
{
    public function __invoke(Request $request, Response $response): void
    {

        $client = (new ZammadClient())->getClient();
        $params = json_decode(file_get_contents('php://input'), true);

        $ticket = $client->resource(ResourceType::TICKET)->get($params['ticketId']);
        $r = $params['priorityId'];
        $ticket->setValue( 'priority_id', $params['priorityId'] );
        $ticket->save();

        (new Response())->success()->send();
    }

}