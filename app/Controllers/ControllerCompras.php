<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\ModelCompras;
use App\Models\EmailCompras;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class ControllerCompras
{

    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleRequest()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

            if (isset($_POST['submitBtn'])) {
                unset($_POST['submitBtn'], $_POST['estoque']);


                $db = new Database();
                $pdo = $db->getConnection();

                try {
                    $model = new ModelCompras($pdo);
                    $model->insertMultiple('compras', $_POST);

                    $stmtmail = $pdo->prepare("SELECT receiver_mail FROM config LIMIT 1");
                    $stmtmail->execute();
                    $email = $stmtmail->fetch(\PDO::FETCH_ASSOC);

                    if ($email && !empty($email['receiver_mail'])) {

                        $emailModel = new EmailCompras($pdo);
                        $userName = 'Equipe de Compras';
                        $emailModel->sendConfirmationToUser($email['receiver_mail'], $userName, $_POST);

                        
                    }

                    // Armazena mensagem única
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Compra registrada com sucesso!'
                    ];



                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                } catch (\Exception $e) {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'text' => 'Erro ao registrar compra: ' . $e->getMessage()
                    ];
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }
        }
    }
}



/*



            if (isset($data['submitBtnEditHeader'])) {
                unset($data['submitBtnEditHeader'], $data['fornecedor_id']);

                $id = $data['compra_id'];
                unset($data['compra_id']);

                $model = new ModelCompras($this->pdo);
                $updated = $model->update($id, $data);

                if ($updated) {

                    if ($updated) {
                        $_SESSION['success'] = "Dados da compra atualizado com sucesso.";
                    } else {
                        $_SESSION['error'] = "Erro ao editar dados.";
                    }

                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }



            if (isset($data['submitBtnAdd'])) {
                unset($data['submitBtnAdd'], $data['estoque']);

                

                // Função para verificar campos vazios, incluindo arrays
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
                    $_SESSION['error'] = "Os seguintes campos estão vazios: " . implode(', ', $camposVazios);
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }

                // Todos os campos foram preenchidos, continua o INSERT
                $compra_id     = $data['compra_id'];
                $fornecedor_id = $data['fornecedor_id'];
                $pagamento_id  = $data['pagamento_id'];
                $cad_data      = $data['cad_data'];
                $previsao      = $data['previsao'];
                $cad_autor     = $data['cad_autor'];
                $produto       = $data['produto'];
                $produto_id    = $data['produto_id'];
                $codigo        = $data['codigo'];
                $medida_id     = $data['medida_id'];
                $quantidade    = $data['quantidade'];
                $preco         = $data['preco'];

                $sql = "INSERT INTO compras (compra_id, fornecedor_id, pagamento_id, cad_data, previsao, produto, produto_id, codigo, medida_id, quantidade, preco, cad_autor)
            VALUES (:compra_id, :fornecedor_id, :pagamento_id, :cad_data, :previsao, :produto, :produto_id, :codigo, :medida_id, :quantidade, :preco, :cad_autor)";
                $stmt = $this->pdo->prepare($sql);

                $inseridos = 0;

                foreach ($produto as $i => $produto) {
                    $stmt->bindValue(':compra_id', $compra_id);
                    $stmt->bindValue(':fornecedor_id', $fornecedor_id);
                    $stmt->bindValue(':pagamento_id', $pagamento_id);
                    $stmt->bindValue(':cad_data', $cad_data);
                    $stmt->bindValue(':previsao', $previsao);
                    $stmt->bindValue(':produto', $produto);
                    $stmt->bindValue(':produto_id', $produto_id[$i], \PDO::PARAM_INT);
                    $stmt->bindValue(':codigo', $codigo[$i]);
                    $stmt->bindValue(':medida_id', $medida_id[$i]);
                    $stmt->bindValue(':quantidade', $quantidade[$i]);
                    $stmt->bindValue(':preco', str_replace(',', '.', $preco[$i]));
                    $stmt->bindValue(':cad_autor', $cad_autor);

                    if ($stmt->execute()) {
                        $inseridos++;
                    } else {
                        var_dump($stmt->errorInfo());
                    }
                }

                if ($inseridos > 0) {
                    $_SESSION['success'] = "Foram adicionados {$inseridos} produtos à compra.";
                } else {
                    $_SESSION['error'] = "Falha ao adicionar os produtos à compra.";
                }

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
}
*/