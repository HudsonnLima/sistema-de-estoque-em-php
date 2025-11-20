<?php

namespace App\Models;

class ModelFuncionarios
{
    protected $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function update($user_id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fields = implode(", ", $fields);

        $sql = "UPDATE users SET $fields WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":" . $key, $value);
        }

        $stmt->bindValue(":user_id", $user_id);

        return $stmt->execute();
    }

    public function insert($table, $data)
    {
        if (empty($data) || !is_array($data)) {
            throw new \Exception("Dados inválidos para inserção");
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        return $stmt->execute($data);
    }
}
