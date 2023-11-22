<?php

declare(strict_types=1);

namespace App;

use PDOException;

class Authorization
{
    /**
     * @var Database
     */
    private Database $database;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * Authorization constructor.
     * @param Database $database
     * @param Session $session
     */
    public function __construct(Database $database, Session $session)
    {
        $this->database = $database;
        $this->session = $session;
    }

    /**
     * @param array $data
     * @return bool
     * @throws AuthorizationException
     */
    public function register(array $params = []): bool
    {
        $params = json_decode(file_get_contents('php://input'), true);

        if (empty($params['user']['username'])) {
            throw new AuthorizationException('The username should not be empty');
        }
        if (empty($params['user']['email'])) {
            throw new AuthorizationException('The email should not be empty');
        }
        if (empty($params['user']['password'])) {
            throw new AuthorizationException('The password should not be empty');
        }

        $statement = $this->database->getConnection()->prepare('SELECT * FROM user WHERE email = :email');
        $statement->execute([
            'email' => $params['user']['email'],
        ]);

        $user = $statement->fetch();
        if (!empty($user)) {
            throw new AuthorizationException('User with such email exists');
        }

        $statement = $this->database->getConnection()->prepare('SELECT * FROM user WHERE username = :username');
        $statement->execute([
            'username' => $params['user']['username'],
        ]);

        $user = $statement->fetch();
        if (!empty($user)) {
            throw new AuthorizationException('User with such username exists');
        }

        try {
            $statement = $this->database->getConnection()->prepare('INSERT INTO user (email, username, password, role) VALUES (:email, :username, :password, :role)');
            $hashedPassword = password_hash($params['user']['password'], PASSWORD_BCRYPT);

            $statement->execute([
                'email' => $params['user']['email'],
                'username' => $params['user']['username'],
                'password' => $hashedPassword,
                'role' => 'user',
            ]);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return true;
    }

    /**
     * @param string $email
     * @param $password
     * @return bool
     * @throws AuthorizationException
     */
    public function login(string $email, $password): bool
    {
        if (empty($email)) {
            throw new AuthorizationException('The email should not be empty');
        }
        if (empty($password)) {
            throw new AuthorizationException('The password should not be empty');
        }

        $statement = $this->database->getConnection()->prepare('SELECT * FROM user WHERE email = :email');
        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetch();

        if (empty($user['id'])) {
            throw new AuthorizationException('User with the email not found');
        }

        if (password_verify($password, $user['password'])) {
            $this->session->setData('user', [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
            ]);
            return true;
        } else {
            throw new AuthorizationException('Incorrect email or password');
        }
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        $this->session->setData('user', null);
        return true;
    }
}