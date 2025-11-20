<?php
namespace App\Models;

class ModelEstoqueSaidas
{
    protected $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function insertMultiple($table, $data)
    {
        if (empty($data) || !is_array($data)) {
            throw new \Exception("Dados inválidos para inserção");
        }

        // Separar campos que são arrays (produtos, quantidades etc) e campos únicos
        $arrayFields = array_filter($data, 'is_array');
        $singleFields = array_filter($data, fn($v) => !is_array($v));

        $numRows = count(reset($arrayFields)); // quantidade de produtos

        for ($i = 0; $i < $numRows; $i++) {
            $row = [];

            foreach ($arrayFields as $key => $arr) {
                $value = $arr[$i] ?? null;

                if ($key === 'preco' && $value !== null) {
                    $value = str_replace(',', '.', $value);
                }

                $row[$key] = $value;
            }

            foreach ($singleFields as $key => $value) {
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
