<?php

declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Request;
use App\Response;
use App\ZammadClient;
use ZammadAPIClient\ResourceType;

class TicketSearch
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = $request->getParams();
        $client = (new ZammadClient())->getClient();

        $searchQuery = $_GET['query'] ?? '';  // Get the search query from the URL parameters

        // $tickets = $client->resource( ResourceType::TICKET )->search($searchQuery.' AND article.attachment.content_type: PDF');
        $tickets = $client->resource( ResourceType::TICKET )->search($searchQuery);


        //$count = $tickets->getTicketsCount();
        $arr = [];
        foreach ($tickets as $key => $ticket) {
            $arr[] = $ticket->getValues();
        }

        (new Response())->success($arr)->send();

    }
}