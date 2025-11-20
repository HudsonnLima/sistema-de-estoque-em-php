<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\ModelUsuarios;

class ControllerUsuarios
{
    protected $pdo;
    protected $model;
    protected $table = 'users';

    public function __construct($pdo = null)
    {
        if (!$pdo) {
            $db = new Database();
            $this->pdo = $db->getConnection();
        } else {
            $this->pdo = $pdo;
        }

        $this->model = new ModelUsuarios($this->pdo);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** TRATAR REQUISIÇÕES */
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Inserir
            if (isset($_POST['submitBtn'])) {
                $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                unset($data['submitBtn']);
                $this->model->insertUser($this->table, $data);
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

            // Atualizar
            if (isset($_POST['updateBtn'])) {
                $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                $user_id = $data['user_id'] ?? null;
                unset($data['updateBtn'], $data['user_id']);
                if ($user_id) {
                    $this->model->updateUser($this->table, $user_id, $data);
                }
                
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

            // Deletar
            if (isset($_POST['deleteBtn'])) {
                $user_id = $_POST['user_id'] ?? null;
                if ($user_id) {
                    $this->model->deleteUser($this->table, $user_id);
                }
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }

    /** BUSCAR USUÁRIO PARA EDIÇÃO */
    public function getUser($user_id)
    {
        return $this->model->getUser($this->table, $user_id);
    }

    /** LISTAR TODOS OS USUÁRIOS */
    public function getAllUsers()
    {
        return $this->model->getAllUsers($this->table);
    }
}
