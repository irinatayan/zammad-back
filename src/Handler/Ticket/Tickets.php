<?php

//declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Database;
use App\Model\TicketUser;
use App\Request;
use App\Response;
use App\ZammadClient;
use PDO;
use PDOException;

class Tickets
{
    public function __invoke(Request $request, Response $response): void
    {

        $params = json_decode(file_get_contents('php://input'), true);

        $config = include_once __DIR__ . '/../../../config/database.php';
        $connection = (new Database($config['dsn'], $config['username'], $config['password']))->getConnection();

        $client = (new ZammadClient())->getClient();

        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 50;
        $state_param = $_GET["state"] ?? "*";
        $sort_by = $_GET['sort_by'] ?? "number";
        $order_by = $_GET['order_by'] ?? "asc";


        $searchQuery = [
            'query' => "state.name:($state_param)",
            'sort_by' => $sort_by,
            'order_by' => $order_by,
            'page' => $page,
            'per_page' => $per_page
        ];

        $response = $client->get('/api/v1/tickets/search', $searchQuery);


        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);


            $tickets = $data['assets']['Ticket'];
            $users = $data['assets']['User'];


            $ticketIds = array_keys($tickets);

            $userTicket = new TicketUser($connection);
            $indexedTicketIdsWithUsername = $userTicket->getTicketsUsername($ticketIds);

            $tickets_with_users_info = [];
            foreach ($tickets as $key => $ticket) {

                $ticket['owner'] = $indexedTicketIdsWithUsername[$ticket['id']];
                $ticket['customer'] = $users[$ticket['customer_id']];
                $tickets_with_users_info[] = $ticket;
            }
            (new Response())->success($tickets_with_users_info)->send();
        } else {
            (new Response())->error(message: $response->getReasonPhrase(), statusCode: 500)->send();
        }
    }
}