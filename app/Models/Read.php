<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Read
{
    private $pdo;
    private $result;
    private $rowCount;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    // Retorna UM único registro
    public function fetch(string $query, array $params = [])
{
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    $this->result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->rowCount = $stmt->rowCount();
    return $this->result;  // <<<< Retornando resultado direto
}

    public function fetchAll(string $query, array $params = [])
{
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    $this->result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $this->rowCount = $stmt->rowCount();
    return $this->result;  // <<<< Retornando resultado direto
}
    public function getResult()
    {
        return $this->result;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }
}
