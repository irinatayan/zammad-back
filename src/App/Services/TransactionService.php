<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
    public function __construct(private Database $db)
    {
    }

    public function create(array $formData): void
    {
        $formattedDate = "{$formData['date']} 00:00:00";

        $this->db->query(
            "insert into transactions(user_id, description, amount, date) 
                                values (:user_id, :description, :amount, :date)",
            [
                'user_id' => $_SESSION['user'],
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formattedDate,
            ]
        );
    }

    public function getUserTransactions(int $length, int $offset): array
    {
        $searchTerm = addcslashes($_GET['s'] ?? '', '%_');

        $params = [
            'user_id' => $_SESSION['user'],
            'description' => "%{$searchTerm}%",
        ];

        $transactionCount = $this->db->query(
            "select count(*)
            from transactions 
            where user_id=:user_id
            and description like :description",
            $params
        )->count();

        $transactions = $this->db->query(
            "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formattedDate 
            FROM transactions 
            WHERE user_id=:user_id
            AND description LIKE :description
            LIMIT {$length} OFFSET {$offset}
            ",
            $params
        )->findAll();

        return [
            $transactions,
            $transactionCount,
        ];
    }

    public function getUserTransaction(string $id)
    {
        return $this->db->query(
            "select *, date_format(date, '%Y-%m-%d') as formattedDate
            from transactions 
            where id = :id and user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        )->find();
    }

    public function update(array $formData, int $id)
    {
        $formattedDate = "{$formData['date']} 00:00:00";
        $this->db->query(
            "UPDATE transactions 
            SET description = :description,
                amount = :amount,
                date = :date
            WHERE id = :id
            AND user_id = :user_id",
            [
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formattedDate,
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->db->query(
            "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        );
    }
}