<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{TicketService, ValidatorService, TransactionService};

class TicketController
{
    public function __construct(
        private TicketService $ticketService,
    ) {
    }

    public function search()
    {
        $this->ticketService->search();
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
}