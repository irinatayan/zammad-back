<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;


class User {
    private PDO $pdo;

    public function __construct(PDO $connection) {
        $this->pdo = $connection;
    }

    public function getUsersByRole($role) {
        try {
            $sql = "SELECT * FROM user WHERE role = :role";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            die("Error: " . $e->getMessage());
        }
    }

    public function getAllUsers() {
        try {
            $sql = "SELECT * FROM user";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }
}
