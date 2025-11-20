<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\EmailAlertaEstoque;

use App\Models\EmailCompras;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use App\Models\Read;

class ControllerEstoqueSaidas
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
            unset($data['submitBtn'], $data['produto']);

            try {
                $this->pdo->beginTransaction();

                echo "<pre style='font-size:14px;background:#111;color:#0f0;padding:10px;border-radius:8px;'>";
                echo "==== INICIANDO REGISTRO DE SAÍDAS ====\n\n";

                // Validação de campos vazios
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
                    echo "⚠️ Campos vazios detectados:\n";
                    print_r($camposVazios);
                    echo "\nTransação abortada.\n";
                    $this->pdo->rollBack();
                    exit;
                }

                unset($data['estoque']);

                // Preparar statements
                $update = $this->pdo->prepare("UPDATE produtos SET estoque = :estoque WHERE produto_id = :produto_id");
                $insert = $this->pdo->prepare("
                INSERT INTO est_saidas 
                (funcionario_id, cad_data, produto_id, quantidade, preco, codigo, medida_id, cad_autor)
                VALUES 
                (:funcionario_id, :cad_data, :produto_id, :quantidade, :preco, :codigo, :medida_id, :cad_autor)
            ");

                $estoqueMinimoAtingido = []; // vira array

                foreach ($data['produto_id'] as $i => $produto_id) {

                    $estoque = (float) ($_POST['estoque'][$i] ?? 0);
                    $saida   = (float) ($data['quantidade'][$i] ?? 0);
                    $alerta  = (float) ($data['alerta'][$i] ?? 0);
                    $novo_estoque = $estoque - $saida;

                    // UPDATE produtos
                    $update->execute([
                        ':estoque' => $novo_estoque,
                        ':produto_id' => $produto_id
                    ]);

                    // INSERT est_saidas
                    $insert->execute([
                        ':funcionario_id' => $data['funcionario_id'],
                        ':cad_data' => $data['cad_data'],
                        ':produto_id' => $produto_id,
                        ':quantidade' => $saida,
                        ':preco' => $data['preco'][$i],
                        ':codigo' => $data['codigo'][$i],
                        ':medida_id' => $data['medida_id'][$i],
                        ':cad_autor' => $data['cad_autor']
                    ]);

                    // Se o estoque atual for menor ou igual ao alerta, adiciona o produto no array
                    if ($novo_estoque <= $alerta) {
                        $estoqueMinimoAtingido[] = $produto_id;
                    }
                }

                if (!empty($estoqueMinimoAtingido)) {

                    // Envio de e-mail alertando sobre o estoque
                    $stmtmail = $this->pdo->prepare("SELECT receiver_mail FROM config LIMIT 1");
                    $stmtmail->execute();
                    $email = $stmtmail->fetch(\PDO::FETCH_ASSOC);

                    if ($email && !empty($email['receiver_mail'])) {
                        $emailModel = new EmailAlertaEstoque($this->pdo);
                        $userName = 'Equipe de Compras';
                        $emailModel->sendConfirmationToUser($email['receiver_mail'], $userName, $_POST);
                    }

                    // Conta corretamente quantos produtos atingiram o mínimo
                    $quantidadeProdutosAtingidos = count($estoqueMinimoAtingido);

                    $mensagem = ($quantidadeProdutosAtingidos > 1)
                        ? 'Saída registrada, mas alguns produtos atingiram o estoque mínimo!'
                        : 'Saída registrada, mas algum produto atingiu o estoque mínimo!';

                    $_SESSION['flash_message'] = [
                        'type' => 'alert',
                        'text' => $mensagem
                    ];
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Saída do produto feita com sucesso!'
                    ];
                }



                $this->pdo->commit();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (\Exception $e) {
                $this->pdo->rollBack();

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
