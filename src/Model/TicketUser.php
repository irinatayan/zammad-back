<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;


class TicketUser {
    private PDO $pdo;

    public function __construct(PDO $connection) {
        $this->pdo = $connection;
    }

    public function create($ticket_id, $user_id) {

    }

    public function update() {
    }

    public function getTicketIdByOwnerId()
    {

    }

    public function getTicketById($ticketId)
    {
        try {
            $sql = "SELECT * FROM ticket_user WHERE ticket_id = :ticket_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;

        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

    }

    public function getOwnerIdByTicketId($ticket_id): ?array
    {
        try {
            $sql = "SELECT user_id FROM ticket_user WHERE ticket_id = :ticket_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;

        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
    public function createOrUpdate($ticketId, $agentId) {

    }
}
