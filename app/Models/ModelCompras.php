<?php

namespace App\Models;

class ModelCompras
{
    protected $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function update($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fields = implode(", ", $fields);

        $sql = "UPDATE compras SET $fields WHERE compra_id = :id";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":" . $key, $value);
        }
        $stmt->bindValue(":id", $id);

        return $stmt->execute();
    }

    public function insertMultiple($table, $data)
    {
        if (empty($data) || !is_array($data)) {
            throw new \Exception("Dados inválidos para inserção");
        }

        $arrayFields = array_filter($data, 'is_array');
        $singleFields = array_filter($data, fn($v) => !is_array($v));

        if (empty($arrayFields)) {
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
            return $stmt->execute($data);
        }

        $numRows = count(reset($arrayFields));

        for ($i = 0; $i < $numRows; $i++) {
            $row = [];

            foreach ($arrayFields as $key => $arr) {
                $value = $arr[$i] ?? null;

                // Corrige preço para o formato do MySQL
                if ($key === 'preco' && $value !== null) {
                    $value = str_replace(',', '.', $value);
                }

                $row[$key] = $value;
            }

            foreach ($singleFields as $key => $value) {
                // Se algum singleField for preço
                if ($key === 'preco' && $value !== null) {
                    $value = str_replace(',', '.', $value);
                }
                $row[$key] = $value;
            }

            $columns = implode(', ', array_keys($row));
            $placeholders = ':' . implode(', :', array_keys($row));
            $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
            $stmt->execute($row);
        }

        return true;
    }
}
