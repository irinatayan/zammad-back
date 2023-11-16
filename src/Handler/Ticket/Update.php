<?php

declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Database;
use App\Model\TicketUser;
use App\Request;
use App\Response;
use PDOException;

class Update
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = json_decode(file_get_contents('php://input'), true);

        $config = include_once __DIR__ . '/../../../config/database.php';
        $connection = (new Database($config['dsn'], $config['username'], $config['password']))->getConnection();


        $owner = (new TicketUser($connection))->getOwnerIdByTicketId($params['ticketId']);

        if ($owner) {
            try {
                $statement = $connection->prepare('UPDATE ticket_user SET user_id = :user_id WHERE ticket_id = :ticket_id');
                $statement->bindParam(':ticket_id', $params["ticketId"]);
                $statement->bindParam(':user_id', $params["agentId"]);
                $statement->execute();
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }

        else {

            $statement = $connection->prepare('INSERT INTO ticket_user (user_id, ticket_id) VALUES (:user_id, :ticket_id)');
            $statement->bindParam(':user_id', $params["agentId"]);
            $statement->bindParam(':ticket_id', $params["ticketId"]);
            $statement->execute();
        }

        (new Response())->success([])->send();
    }
}