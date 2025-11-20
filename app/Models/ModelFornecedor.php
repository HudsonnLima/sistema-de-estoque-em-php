<?php

namespace App\Models;

class ModelFornecedor
{
    protected $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        return $stmt->execute($data);
    }

    public function update($table, $data, $id)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fields = implode(', ', $fields);
        $stmt = $this->db->prepare("UPDATE $table SET $fields WHERE fornecedor_id = :id");
        $data['id'] = $id;

        return $stmt->execute($data);
    }
}
