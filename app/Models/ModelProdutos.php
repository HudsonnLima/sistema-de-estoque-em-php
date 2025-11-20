<?php

namespace App\Models;

class ModelProdutos
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert(array $data)
    {
        $sql = "INSERT INTO produtos 
        (produto, descricao, codigo, medida_id, prioridade, produto_status, alerta, estoque_maximo, cad_data, cad_autor)
        VALUES (:produto, :descricao, :codigo, :medida_id, :prioridade, :produto_status, :alerta, :estoque_maximo, :cad_data, :cad_autor)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':produto'        => $data['produto'],
            ':descricao'      => $data['descricao'],
            ':codigo'         => $data['codigo'],
            ':medida_id'      => $data['medida_id'],
            ':prioridade'     => $data['prioridade'],
            ':produto_status' => $data['produto_status'],
            ':alerta'         => $data['alerta'],
            ':estoque_maximo' => $data['estoque_maximo'],
            ':cad_data'       => $data['cad_data'],
            ':cad_autor'      => $data['cad_autor']
        ]);
    }

    public function atualizar(array $data)
{
    // garante que temos produto_id
    if (empty($data['produto_id'])) {
        throw new \InvalidArgumentException('produto_id é necessário para atualizar.');
    }

    $sql = "UPDATE produtos SET
        produto = :produto,
        descricao = :descricao,
        codigo = :codigo,
        medida_id = :medida_id,
        prioridade = :prioridade,
        produto_status = :produto_status,
        alerta = :alerta,
        estoque_maximo = :estoque_maximo
    WHERE produto_id = :produto_id";

    $stmt = $this->pdo->prepare($sql);

    // monta array com parâmetros, evitando notices caso falte campo
    $params = [
        ':produto' => $data['produto'] ?? null,
        ':descricao' => $data['descricao'] ?? null,
        ':codigo' => $data['codigo'] ?? null,
        ':medida_id' => $data['medida_id'] ?? null,
        ':prioridade' => $data['prioridade'] ?? null,
        ':produto_status' => $data['produto_status'] ?? null,
        ':alerta' => $data['alerta'] ?? null,
        ':estoque_maximo' => $data['estoque_maximo'] ?? null,
        ':produto_id' => $data['produto_id']
    ];

    $stmt->execute($params);
}

public function getProdutoById($id)
{
    $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE produto_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
}
