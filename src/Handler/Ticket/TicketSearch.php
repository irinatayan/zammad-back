<?php

declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Database;
use App\Model\TicketUser;
use App\Request;
use App\Response;
use App\ZammadClient;
use ZammadAPIClient\ResourceType;

class TicketSearch
{
    public function __invoke(Request $request, Response $response): void
    {
        $config = include_once __DIR__ . '/../../../config/database.php';
        $connection = (new Database($config['dsn'], $config['username'], $config['password']))->getConnection();

        $params = $request->getParams();
        $client = (new ZammadClient())->getClient();

        $searchQuery = $_GET['query'] ?? '';  // Get the search query from the URL parameters

        // $tickets = $client->resource( ResourceType::TICKET )->search($searchQuery.' AND article.attachment.content_type: PDF');
        $tickets = $client->resource( ResourceType::TICKET )->search($searchQuery);


        $arr = [];
        $ticketIndexes = [];
        foreach ($tickets as $key => $ticket) {
            $ticketValues = $ticket->getValues();
            $ticketIndexes[] = $ticketValues['id'];
            $arr[] = $ticketValues;
        }

        $userTicket = new TicketUser($connection);
        $indexedTicketIdsWithUsername = $userTicket->getTicketsUsername($ticketIndexes);

        foreach ($arr as $key => &$ticket) {
            $ticket['owner'] = $indexedTicketIdsWithUsername[$ticket['id']]['username'];
        }

        (new Response())->success($arr)->send();

    }
}