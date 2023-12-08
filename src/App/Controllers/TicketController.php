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
}