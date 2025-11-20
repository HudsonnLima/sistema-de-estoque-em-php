<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\ModelFornecedor;
use PDO;

require_once BASE_HELPERS . '/ValidacaoHelper.php';

class ControllerFornecedor
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleRequest()
    {
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Verifica se o CNPJ está preenchido
        $cnpj = trim($data['fornecedor_cnpj'] ?? '');
        $fornecedorId = isset($data["fornecedor_id"]) ? intval($data["fornecedor_id"]) : null;

        if (empty($cnpj) || !validarCnpj($cnpj)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'CNPJ inválido.'
            ];
            $_SESSION['form_data'] = $data;
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }

        // Verifica se o CNPJ já existe no banco
        $stmt = $this->pdo->prepare("SELECT fornecedor_id, fornecedor_cnpj FROM fornecedor WHERE fornecedor_cnpj = :cnpj LIMIT 1");
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->execute();
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            $fornecedorExistenteId = (int)$exists['fornecedor_id'];
            $fornecedorAtualId = (int)$fornecedorId;
            if ($fornecedorExistenteId !== $fornecedorAtualId) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'CNPJ já cadastrado em outro fornecedor.'
                ];
                $_SESSION['form_data'] = $data;
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }

        // campos opcionais não contam para obrigatoriedade
        $camposOpcionais = ['fornecedor_telefone', 'fornecedor_celular', 'fornecedor_email', 'fornecedor_site'];
        foreach ($camposOpcionais as $campo) {
            unset($data[$campo]);
        }

        if (count(array_filter($data, fn($v) => $v === null || $v === '')) > 0) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Por favor, preencha todos os campos obrigatórios.'
            ];
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }

        if (isset($_POST['submitBtn'])) {
            // cadastro (se necessário)
            try {
                $model = new ModelFornecedor($this->pdo);
                //$model->insert('fornecedor', $_POST);
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Fornecedor cadastrado com sucesso!'
                ];
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Erro ao cadastrar fornecedor: ' . $e->getMessage()
                ];
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }

        if (isset($_POST['editSubmitBtn'])) {
            try {
                $model = new ModelFornecedor($this->pdo);
                $fornecedorId = (int)($_POST['fornecedor_id'] ?? 0);
                $dataUpdate = $_POST;
                unset($dataUpdate['fornecedor_id'], $dataUpdate['editSubmitBtn']);
                $model->update('fornecedor', $dataUpdate, $fornecedorId);

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Fornecedor atualizado com sucesso!'
                ];
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Erro ao atualizar fornecedor: ' . $e->getMessage()
                ];
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
}
