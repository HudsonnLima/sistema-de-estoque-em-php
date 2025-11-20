<?php
namespace App\Controllers;
use App\Core\Database;
use App\Models\ModelProdutos;
use PDO;

class ControllerProdutos
{
    private $model;
    private $pdo; // <-- adicionar isso

    public function __construct($pdo)
    {
        $this->pdo = $pdo;        // <-- salvar o PDO
        $this->model = new ModelProdutos($pdo);
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);


            // Usar $this->pdo !!!!!
            $stmt = $this->pdo->query("DESCRIBE produtos");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $errors = [];

            foreach ($columns as $field) {
                if (isset($data[$field]) && empty($data[$field])) {
                    $errors[$field] = "O campo {$field} é obrigatório.";
                }
            }

            if (!empty($errors)) {
                $_SESSION['form_data'] = $data;
                $_SESSION['form_errors'] = $errors;

                $_SESSION['flash_message'] = [
                    'type' => 'warning',
                    'text' => 'Por favor, preencha todos os campos obrigatórios.'
                ];

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

            try {

                if (!empty($data['produto_id'])) {
                    $this->model->atualizar($data);
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Produto editado com sucesso!'
                    ];
                } else {
                    $this->model->insert($data);
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Produto adicionado com sucesso!'
                    ];
                }

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;

            } catch (\Exception $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Erro: ' . $e->getMessage()
                ];

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
}
