<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\ModelFuncionarios;

class ControllerFuncionarios
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

            /*
             * CADASTRAR FUNCIONÁRIO
             */
            if (isset($data['submitBtn'])) {

                unset($data['submitBtn']);

                try {
                    $db = new Database();
                    $pdo = $db->getConnection();

                    $model = new ModelFuncionarios($pdo);
                    $model->insert('users', $data);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Funcionário cadastrado com sucesso!'
                    ];

                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                } catch (\Exception $e) {

                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'text' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()
                    ];

                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }

            /*
             * EDITAR FUNCIONÁRIO
             */
            if (isset($data['submitBtnEdit'])) {

                unset($data['submitBtnEdit']);

                $user_id = $data['user_id'];
                unset($data['user_id']);

                try {

                    $model = new ModelFuncionarios($this->pdo);
                    $updated = $model->update($user_id, $data);

                    if ($updated) {
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'text' => 'Funcionário atualizado com sucesso!'
                        ];
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'error',
                            'text' => 'Nenhuma alteração realizada.'
                        ];
                    }

                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;

                } catch (\Exception $e) {

                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'text' => 'Erro ao atualizar funcionário: ' . $e->getMessage()
                    ];

                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }
        }
    }
}
