<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{ValidatorService, TransactionService};

class TransactionController
{
    public function __construct(
        private TemplateEngine $view,
        private ValidatorService $validatorService,
        private TransactionService $transactionService,
    ) {
    }

    public function createView(): void
    {
        echo $this->view->render("transactions/create.php");
    }

    public function create(): void
    {
        $this->validatorService->validateTransaction($_POST);
        $this->transactionService->create($_POST);
        redirectTo('/');
    }

    public function editView(array $params): void
    {
        $transaction = $this->transactionService->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo('/');
        }
        echo $this->view->render("transactions/edit.php", ['transaction' => $transaction]);
    }

    public function update(array $params): void
    {
        $transaction = $this->transactionService->getUserTransaction($params['transaction']);

        if (!$transaction) {
            redirectTo('/');
        }
        $this->validatorService->validateTransaction($_POST);

        $this->transactionService->update($_POST, $transaction['id']);

        redirectTo($_SERVER['HTTP_REFERER']);
    }

    public function delete(array $params): void
    {
        $this->transactionService->delete((int) $params['transaction']);
        redirectTo('/');
    }
}