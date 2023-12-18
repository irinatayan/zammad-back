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
    public function search(string $query): array
    {
        // $tickets = $client->resource( ResourceType::TICKET )->search($searchQuery.' AND article.attachment.content_type: PDF');
        $tickets = $this->client->resource( ResourceType::TICKET )->search($query);
        $valuedTickets = [];
        $ticketIndexes = [];
        foreach ($tickets as $ticket) {
            $ticketValues = $ticket->getValues();
            $ticketIndexes[] = $ticketValues['id'];
            $valuedTickets[] = $ticketValues;
        }

        $indexedTicketIdsWithUsername = $this->getTicketsUsername($ticketIndexes);

        foreach ($valuedTickets as $key => &$ticket) {
            $ticket['owner'] = $indexedTicketIdsWithUsername[$ticket['id']]['username'];
        }
        return $valuedTickets;
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

    public function getTicket($id): array
    {
        $ticket = $this->client->resource(ResourceType::TICKET)->get($id);
        $ticket = !empty($ticket->getValues()) ? $ticket->getValues() : null;
        $articles = $this->getTicketArticles($id);
        $agents = $this->userService->getUsersByRole('user');
        $owner = $this->getOwnerIdByTicketId($id);
        $states = $this->filterStates();
        return [
            "ticket" => $ticket,
            "articles" => $articles,
            "agents" => $agents,
            "owner" => $owner,
            "states" => $states
        ];;
    }

    private function getTicketArticles($ticketId): array
    {
        $articles = $this->client->resource(ResourceType::TICKET_ARTICLE)->getForTicket($ticketId);

        $articlesArr = [];

        foreach ($articles as $article) {
            $articlesArr[] = $article->getValues();
        }
        return $articlesArr;
    }

    public function getOwnerIdByTicketId($ticketId): ?array
    {
            $sql = "SELECT user_id FROM ticket_user WHERE ticket_id = :ticket_id";
            $owner = $this->db->query($sql,             [
                "ticket_id" => $ticketId,
            ])->find();
            return $owner ?: null;
    }

    private function filterStates(): array
    {
        $states = $this->client->resource(ResourceType::TICKET_STATE)->all();
        $allowedStates = ["closed", "open", "pending reminder"];
        $statesArr = [];

        foreach ($states as $state) {
            $stateValues = $state->getValues();
            ['id' => $id, 'name' => $name] = $stateValues;

            if (in_array($name, $allowedStates)) {
                $statesArr[] = $stateValues;
            }
        }

        return $statesArr;
    }

    function updateTicketOwner($ticketId, $ownerId)
    {
        $owner = $this->getOwnerIdByTicketId($ticketId);

        if ($owner) {
            $sql = 'UPDATE ticket_user SET user_id = :user_id WHERE ticket_id = :ticket_id';
            $this->db->query($sql, [
                'ticket_id' => $ticketId,
                'user_id' => $ownerId

            ]);
        }

        else {
            $sql = 'INSERT INTO ticket_user (user_id, ticket_id) VALUES (:user_id, :ticket_id)';
            $this->db->query($sql, [
                'ticket_id' => $ticketId,
                'user_id' => $ownerId

            ]);
        }
    }

}