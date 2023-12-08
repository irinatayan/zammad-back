<?php

namespace App\Services;

use App\Model\TicketUser;
use App\Response;
use Framework\Database;
use ZammadAPIClient\Client;
use ZammadAPIClient\ResourceType;

class TicketService
{
    public function __construct(private Database $db, private Client $client)
    {

    }
    public function search()
    {
        $searchQuery = $_GET['query'] ?? '';
        // $tickets = $client->resource( ResourceType::TICKET )->search($searchQuery.' AND article.attachment.content_type: PDF');
        $tickets = $this->client->resource( ResourceType::TICKET )->search($searchQuery);


        $arr = [];
        $ticketIndexes = [];
        foreach ($tickets as $key => $ticket) {
            $ticketValues = $ticket->getValues();
            $ticketIndexes[] = $ticketValues['id'];
            $arr[] = $ticketValues;
        }

//        $userTicket = new TicketUser($connection);
//        $indexedTicketIdsWithUsername = $userTicket->getTicketsUsername($ticketIndexes);
//
//        foreach ($arr as $key => &$ticket) {
//            $ticket['owner'] = $indexedTicketIdsWithUsername[$ticket['id']]['username'];
//        }

//        (new Response())->success($arr)->send();

        echo json_encode([
            'message' => 'search',
            'data' => $arr
        ]);
    }
}