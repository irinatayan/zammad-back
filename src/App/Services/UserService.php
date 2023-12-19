<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    private ?array $user;
    public function __construct(
        private readonly Database $db,
        private readonly JWTCodecService $JWTCodec,
        private readonly RefreshTokenService $refreshTokenService
    )
    {
        $this->user = null;
    }

    public function setUser (?array $user): void
    {
        $this->user = $user;
    }

    public function getUser (): ?array
    {
        return $this->user;
    }

    public function getById($id)
    {

        $sql = "SELECT *
                FROM user
                WHERE id = :id";

        return $this->db->query(
            $sql,
            [
                "id" => $id
            ]
        )->find();

    }

    public function isEmailTaken(string $email)
    {
        $emailCount = $this->db->query(
            "select count(*) from user where email=:email",
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
        $this->isEmailTaken($formData['email']);
        $this->db->query(
            "insert into user (email, username, password, role)
              values (:email, :username, :password, :role)",
            [
                "email" => $formData['email'],
                "username" => $formData['username'],
                "password" => $password,
                "role" => "user"
            ]
        );
    }

    public function login(array $formData): void
    {
        $user = $this->db->query(
            "select * from user where email=:email",
            [
                "email" => $formData['email']
            ]
        )->find();

        $passwordMatch = password_verify($formData['password'], $user['password'] ?? '');

        if (!$user || !$passwordMatch) {
            throw new ValidationException(['password' => ['Invalid credentials']]);
        }

        $payload = [
            "sub" => $user['id'],
            "email" => $user["email"],
            "exp" => time() + 20
        ];

        $access_token = $this->JWTCodec->encode($payload);

        $refresh_token_expiry = time() + 432000; //5 days

        $refresh_token = $this->JWTCodec->encode([
            "sub" => $user['id'],
            "exp" => $refresh_token_expiry
        ]);

        $this->refreshTokenService->create($refresh_token, $refresh_token_expiry);

        echo json_encode([
            "access_token" => $access_token,
            "refresh_token" => $refresh_token,
            "user" => $user
        ]);

    }

    public function logout():void
    {
        unset($_SESSION['user']);
        session_regenerate_id();
    }

    public function refreshToken(array $formData): void
    {

        if (!array_key_exists("token", $formData)) {
            http_response_code(400);
            echo json_encode([
                "message" => "missing token",
            ]);
            exit;
        }

        try {
            $payload = $this->JWTCodec->decode($formData['token']);
        } catch (Exception) {
            http_response_code(400);
            echo json_encode([
                'message' => 'invalid token',
            ]);
            exit();
        }

        $user_id = $payload['sub'];

        $refresh_token = $this->refreshTokenService->getByToken($formData['token']);

        if ($refresh_token === false) {
            http_response_code(400);
            echo json_encode(["message" => "invalid token (not in white-list)"]);
            exit();
        }

        $user = $this->getById($user_id);

        if ($user === false) {
            http_response_code(401);
            echo json_encode(['message' => 'invalid authentication']);
            exit();
        }

        $payload = [
            "sub" => $user['id'],
            "email" => $user["email"],
            "exp" => time() + 20
        ];

        $access_token = $this->JWTCodec->encode($payload);

        $refresh_token_expiry = time() + 432000; //5 days

        $refresh_token = $this->JWTCodec->encode([
            "sub" => $user['id'],
            "exp" => $refresh_token_expiry
        ]);


        echo json_encode([
            "access_token" => $access_token,
            "refresh_token" => $refresh_token,
            "user" => $user
        ]);

        $this->refreshTokenService->delete($formData['token']);
        $this->refreshTokenService->create($refresh_token, $refresh_token_expiry);


    }

    public function getUsersByRole($role): array
    {
        $sql = "SELECT * FROM user WHERE role = :role";
        return $this->db->query($sql,
            [
                "role" => $role,
            ]
        )->findAll();
    }

    public function getAllUsers():array
    {
        $sql = "SELECT * FROM user";
        return $this->db->query($sql)->findAll();
    }
}