<?php

namespace App\Services;
use App\Model\TicketUser;

use App\Response;
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

    public function getAll(array $query)
    {
        $response = $this->client->get('/api/v1/tickets/search', $query);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);


            $tickets = $data['assets']['Ticket'];
            $users = $data['assets']['User'];


            $ticketIds = array_keys($tickets);

            $indexedTicketIdsWithUsername = $this->getTicketsUsername($ticketIds);


            $states = $this->client->resource(ResourceType::TICKET_STATE)->all();
            $statesArr = [];
            foreach ($states as $state) {
                $stateValues = $state->getValues();
                ['id' => $id, 'name' => $name] = $stateValues;

                if ($name === "closed" || $name === "open" || $name === "pending reminder") {
                    $statesArr[$id] = $name;
                }
            }

            $tickets_with_users_info = [];
            foreach ($tickets as $key => $ticket) {
                $ticket['owner'] = $indexedTicketIdsWithUsername[$ticket['id']];
                $ticket['customer'] = $users[$ticket['customer_id']];
                $ticket['state'] = $statesArr[$ticket['state_id']];
                $tickets_with_users_info[] = $ticket;
            }
            return [
                "tickets" => $tickets_with_users_info,
                "user" => $this->userService->getUser()
            ];

        } else {
            (new Response())->error(message: $response->getReasonPhrase(), statusCode: 500)->send();
        }
    }

    private function getTicketsUsername($ticketsIds): array
    {
        if (empty($ticketsIds)) {
            return [];
        };
        $ticketIdsString = implode(',', $ticketsIds);

        $sql = "SELECT tu.ticket_id, u.username
                FROM ticket_user tu
                JOIN user u ON tu.user_id = u.id
                WHERE tu.ticket_id IN ($ticketIdsString)";

        $ticketIdsWithUsername = $this->db->query($sql)->findAll();
        $indexedTicketIdsWithUsername = [];
        foreach ($ticketIdsWithUsername as $row) {
            $indexedTicketIdsWithUsername[$row['ticket_id']] = $row;
        }
        return $indexedTicketIdsWithUsername;
    }
}