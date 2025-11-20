<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\ModelEstoqueEntradas;
use App\Models\Read;

class ControllerEstoqueEntradas
{
    protected $pdo;
    protected $read;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->read = new Read();
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitBtn'])) {

            $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            unset($data['submitBtn']);

            try {
               
                $camposVazios = [];
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $i => $v) {
                            if ($v === null || trim($v) === '') {
                                $camposVazios[] = "{$key}[{$i}]";
                            }
                        }
                    } else {
                        if ($value === null || trim($value) === '') {
                            $camposVazios[] = $key;
                        }
                    }
                }

                if (!empty($camposVazios)) {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'text' => "Os seguintes campos estão vazios: " . implode(', ', $camposVazios)
                    ];
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }

                // 2️⃣ Preparar dados para est_entradas (remover estoque e estado)
                $model = new ModelEstoqueEntradas($this->pdo);
                $insertData = $data;
                unset($insertData['estoque']);
                unset($insertData['estado']);

                // Inserir produtos em est_entradas
                $model->insertMultiple('est_entradas', $insertData);

                // 3️⃣ Atualizar estoque na tabela produtos
                foreach ($data['produto_id'] as $i => $produto_id) {
                    $novoEstoque = $data['estoque'][$i] ?? 0;
                    $novoPreco   = $data['preco'][$i] ?? 0;

                    $stmt = $this->pdo->prepare("UPDATE produtos SET estoque = :estoque, preco = :preco WHERE produto_id = :id");
                    $stmt->execute([
                        'estoque' => $novoEstoque,
                        'preco'   => $novoPreco,
                        'id'      => $produto_id
                    ]);
                }

                // 4️⃣ Atualizar estado da compra
                $compra_id = $data['compra_id'] ?? null;
                if ($compra_id) {
                    $estado = $data['estado'] ?? 0;
                    $stmt = $this->pdo->prepare("UPDATE compras SET estado = :estado WHERE compra_id = :id");
                    $stmt->execute([
                        'estado' => $estado,
                        'id' => $compra_id
                    ]);
                }

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Produtos adicionados ao estoque com sucesso!'
                ];

                header('Location: ' . BASE_URL . '/estoque/entradas');
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Erro ao adicionar produtos: ' . $e->getMessage()
                ];

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
}
