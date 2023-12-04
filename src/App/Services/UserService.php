<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    public function __construct(private Database $db)
    {
    }

    public function isEmailTaken(string $email)
    {
        $emailCount = $this->db->query(
            "select count(*) from users where email=:email",
            ["email" => $email]
        )->count();

        if ($emailCount > 0) {
            throw new ValidationException([
                'email' => ['Email taken'],
            ]);
        }
    }

    public function create(array $formData): void
    {
        $password = password_hash($formData['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $this->db->query(
            "insert into users (email, password, age, country, social_media_url)
              values (:email, :password, :age, :country, :social_media_url)",
            [
                "email" => $formData['email'],
                "password" => $password,
                "age" => $formData['age'],
                "country" => $formData['country'],
                "social_media_url" => $formData['socialMediaURL'],
            ]
        );

        session_regenerate_id();
        $_SESSION['user'] = $this->db->id();
    }

    public function login(array $formData): void
    {
        $user = $this->db->query(
            "select * from users where email=:email",
            [
                "email" => $formData['email']
            ]
        )->find();

        $passwordMatch = password_verify($formData['password'], $user['password'] ?? '');

        if (!$user || !$passwordMatch) {
            throw new ValidationException(['password' => ['Invalid credentials']]);
        }

        session_regenerate_id();

        $_SESSION['user'] = $user['id'];
    }

    public function logout():void
    {
        unset($_SESSION['user']);
        session_regenerate_id();
    }
}