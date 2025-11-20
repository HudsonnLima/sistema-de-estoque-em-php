<?php

namespace App\Models;

class ModelLogin
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca o usuário no banco pelo nome de usuário ou e-mail.
     */
    public function findUser($username)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users 
            WHERE user_email = :username 
               OR user_name = :username
            LIMIT 1
        ");
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
