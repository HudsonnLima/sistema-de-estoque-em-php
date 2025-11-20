<?php

class Compra
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCompraById($id)
    {
        $sql = "SELECT c.compra_id, c.pagamento_id, c.cad_data, c.previsao,
                       f.fornecedor_razao AS fornecedor,
                       u.user_name AS usuario
                FROM compras c
                LEFT JOIN fornecedor f ON f.fornecedor_id = c.fornecedor_id
                LEFT JOIN users u ON u.user_id = c.cad_autor
                WHERE c.compra_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCompra($data)
    {
        $sql = "UPDATE compras
                SET pagamento_id = :pagamento,
                    previsao = :previsao,
                    cad_data = :data
                WHERE compra_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':pagamento' => $data['pagamento'],
            ':previsao'  => $data['previsao'],
            ':data'      => $data['data'],
            ':id'        => $data['id']
        ]);
    }
}
