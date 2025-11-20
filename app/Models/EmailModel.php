<?php
namespace App\Models;

use PDO;

class EmailModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function checkEmailExists(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE user_email = :user_mail");
        $stmt->bindParam(':user_email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
