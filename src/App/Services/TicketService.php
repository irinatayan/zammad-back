<?php

namespace App\Services;
use App\Model\TicketUser;

use Framework\Database;
use ZammadAPIClient\Client;
use ZammadAPIClient\ResourceType;

class TicketService
{
    public function __construct(
        private Database $db,
        private readonly Client $client,
        private readonly UserService $userService
    )
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

        $user = $this->userService->getUser();

        echo json_encode([
            'message' => 'search',
            'data' => [
                "tickets" => $arr,
                "user" => $this->userService->getUser()
            ]
        ]);
    }
}