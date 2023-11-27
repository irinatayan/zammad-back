<?php

declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Database;
use App\Model\TicketUser;
use App\Model\User;
use App\Request;
use App\Response;
use App\ZammadClient;
use PDOException;
use ZammadAPIClient\ResourceType;

class StatesPrioritiesAgents
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = json_decode(file_get_contents('php://input'), true);

        $config = include_once __DIR__ . '/../../../config/database.php';
        $connection = (new Database($config['dsn'], $config['username'], $config['password']))->getConnection();
        $client = (new ZammadClient())->getClient();


        $states = $client->resource(ResourceType::TICKET_STATE)->all();
        $priorities = $client->resource(ResourceType::TICKET_PRIORITY)->all();
        $agents = (new User($connection))->getUsersByRole('user');

        $statesArr = [];
        foreach ($states as $state) {
            $stateValues = $state->getValues();
            ['id' => $id, 'name' => $name] = $stateValues;

            if ($name === "closed" || $name === "open" || $name === "pending reminder") {
                $statesArr[] = $stateValues;
            }
        }

        $prioritiesArr = [];
        foreach ($priorities as $priority) {
            $stateValues = $priority->getValues();
            $prioritiesArr[] = $stateValues;
        }


        $data = [
            "agents" => $agents,
            "priorities" => $prioritiesArr,
            "states" => $statesArr
        ];



        (new Response())->success($data)->send();
    }
}