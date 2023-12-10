<?php

namespace App\Services;
use Framework\Database;
use PDO;

class RefreshTokenService
{

    public function __construct(private Database $db, private string $key)
    {

    }

    public function create(string $token, int $expiry): void
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "INSERT INTO refresh_token (token_hash, expires_at)
                VALUES (:token_hash, :expires_at)";

        $this->db->query($sql, [
            "token_hash" => $hash,
            "expires_at" => $expiry
        ]);
    }

    public function delete(string $token): void
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "DELETE FROM refresh_token
                WHERE token_hash = :token_hash";

        $this->db->query($sql, [
            "token_hash" => $hash
        ]);
    }

    public function getByToken(string $token): array | false
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "SELECT *
                FROM refresh_token
                WHERE token_hash = :token_hash";

        return $this->db->query($sql, [
            "token_hash" => $hash,
        ])->find();

    }

    public function deleteExpired(): int
    {
        $sql = "DELETE FROM refresh_token
                WHERE expires_at < UNIX_TIMESTAMP()";

        $this->db->query($sql);

    }
}