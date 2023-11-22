<?php

declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Database;
use App\Model\TicketUser;
use App\Request;
use App\Response;
use App\ZammadClient;
use App\Model\User;
use ZammadAPIClient\ResourceType;

class Ticket
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = $request->getParams();
        $client = (new ZammadClient())->getClient();

        $config = include_once __DIR__ . '/../../../config/database.php';
        $connection = (new Database($config['dsn'], $config['username'], $config['password']))->getConnection();

        $ticket = $this->getTicket($client, $params['id']);

        $articles = $this->getTicketArticles($client, $params['id']);

        $agents = (new User($connection))->getUsersByRole('user');

        $owner = (new TicketUser($connection))->getOwnerIdByTicketId($ticket['id']);

        $states = $this->getStates($client);



        $data = [
            "ticket" => $ticket,
            "articles" => $articles,
            "agents" => $agents,
            "owner" => $owner,
            "states" => $states
        ];
        (new Response())->success($data)->send();

    }

    private function getTicket($client, $ticketId): ?array
    {
        $ticket = $client->resource(ResourceType::TICKET)->get($ticketId);

        return !empty($ticket->getValues()) ? $ticket->getValues() : null;
    }

    private function getTicketArticles($client, $ticketId): array
    {
        $articles = $client->resource(ResourceType::TICKET_ARTICLE)->getForTicket($ticketId);

        $articlesArr = [];

        foreach ($articles as $article) {
            $articlesArr[] = $article->getValues();
        }
        return $articlesArr;
    }

    private function getStates($client): ?array
    {
        $states = $client->resource(ResourceType::TICKET_STATE)->all();

        $stateArr = [];

        foreach ($states as $state) {
            $stateValues = $state->getValues();
            ['id' => $id, 'name' => $name] = $stateValues;

            if ($name === "closed" || $name === "open" || $name === "pending reminder") {
                $stateArr[] = $stateValues;
            }
        }
        return $stateArr;
    }
}