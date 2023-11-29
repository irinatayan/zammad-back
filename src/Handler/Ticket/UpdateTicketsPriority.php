<?php

declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Database;
use App\Model\TicketUser;
use App\Request;
use App\Response;
use App\ZammadClient;
use PDOException;
use ZammadAPIClient\ResourceType;

class UpdateTicketsPriority
{
    public function __invoke(Request $request, Response $response): void
    {
        $client = (new ZammadClient())->getClient();
        $params = json_decode(file_get_contents('php://input'), true);


        foreach ($params['tickets'] as $ticketId) {
            $ticket = $client->resource(ResourceType::TICKET)->get($ticketId);
            $ticket->setValue( 'priority_id', $params['priorityId'] );
            $ticket->save();
        }

        (new Response())->success()->send();
    }
}