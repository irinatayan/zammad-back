<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\{TicketService, UserService};
use ZammadAPIClient\ResourceType;

readonly class TicketController
{
    public function __construct(
        private TicketService $ticketService,
        private UserService $userService
    ) {
    }

    public function search(): void
    {
        $searchQuery = $_GET['query'] ?? '';
        $tickets = $this->ticketService->search($searchQuery);
        echo json_encode([
            'message' => 'search',
            'data' => [
                "tickets" => $tickets,
                "user" => $this->userService->getUser()
            ]
        ]);
    }

    public function getAll()
    {
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
        $tickets = $this->ticketService->getAll($searchQuery);

        echo json_encode([
            'message' => 'search',
            'data' => $tickets
        ]);
    }

    public function getTicket(): void
    {
        $id = $_GET['id'] ?? '';

        $ticket = $this->ticketService->getTicket($id);
        echo json_encode([
            'data' => $ticket
        ]);
    }

    public function updateTicketOwner(): void
    {
        $params = json_decode(file_get_contents('php://input'), true);
        $this->ticketService->updateTicketOwner($params["ticketId"], $params["agentId"]);
    }

    public function updateTicketPriority(): void
    {
        $params = json_decode(file_get_contents('php://input'), true);
        $this->ticketService->updateTicketPriority($params['ticketId'], $params['priorityId']);

    }
}