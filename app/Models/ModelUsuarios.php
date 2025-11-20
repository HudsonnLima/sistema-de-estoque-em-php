<?php

namespace App\Models;

class ModelUsuarios
{
    protected $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /** INSERIR USUÁRIO */
    public function insertUser($table, $data)
    {
        try {
            $data['user_name'] = $this->formatarNome($data['user_name']);

            if (!$this->validarSenha($data['user_pass'])) return false;

            if ($this->emailExists($table, $data['user_email'])) {
                $_SESSION['erro'] = "O e-mail informado já está cadastrado.";
                $_SESSION['form_data'] = $data;
                return false;
            }

            $data['user_pass'] = password_hash($data['user_pass'], PASSWORD_BCRYPT);

            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $stmt = $this->db->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})");

            if (!$stmt->execute($data)) {
                $this->logErro("insertUser", $stmt->errorInfo());
                $_SESSION['erro'] = "Erro ao inserir usuário.";
                return false;
            }

            $_SESSION['sucesso'] = "Usuário {$data['user_name']} cadastrado com sucesso!";
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            $this->logErro("insertUser", $e->getMessage());
            $_SESSION['erro'] = "Erro na inserção de usuário.";
            return false;
        }
    }

    /** ATUALIZAR USUÁRIO */
    public function updateUser($table, $user_id, $data)
    {
        try {
            if (isset($data['user_pass']) && $data['user_pass'] !== '') {
                if (!$this->validarSenha($data['user_pass'])) return false;
                $data['user_pass'] = password_hash($data['user_pass'], PASSWORD_BCRYPT);
            } else {
                unset($data['user_pass']);
            }

            $set = [];
            foreach ($data as $col => $val) {
                $set[] = "{$col} = :{$col}";
            }
            $setStr = implode(', ', $set);
            $data['user_id'] = $user_id;

            $stmt = $this->db->prepare("UPDATE {$table} SET {$setStr} WHERE user_id = :user_id");

            if (!$stmt->execute($data)) {
                $this->logErro("updateUser", $stmt->errorInfo());
                $_SESSION['erro'] = "Erro ao atualizar usuário.";
                return false;
            }

            $_SESSION['sucesso'] = "Usuário atualizado com sucesso!";
            return true;
        } catch (\PDOException $e) {
            $this->logErro("updateUser", $e->getMessage());
            $_SESSION['erro'] = "Erro na atualização de usuário.";
            return false;
        }
    }


    /** DELETAR USUÁRIO */
    public function deleteUser($table, $user_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE user_id = :user_id");
            if (!$stmt->execute(['user_id' => $user_id])) {
                $this->logErro("deleteUser", $stmt->errorInfo());
                $_SESSION['erro'] = "Erro ao deletar usuário.";
                return false;
            }

            $_SESSION['sucesso'] = "Usuário deletado com sucesso!";
            return true;
        } catch (\PDOException $e) {
            $this->logErro("deleteUser", $e->getMessage());
            $_SESSION['erro'] = "Erro na exclusão de usuário.";
            return false;
        }
    }

    /** BUSCAR USUÁRIO */
    public function getUser($table, $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /** LISTAR TODOS OS USUÁRIOS */
    public function getAllUsers($table)
    {
        $stmt = $this->db->query("SELECT * FROM {$table} ORDER BY user_name ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** VERIFICA SE O EMAIL JÁ EXISTE */
    private function emailExists($table, $email)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$table} WHERE user_email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    /** VALIDAR SENHA */
    private function validarSenha($senha)
    {
        if (strlen($senha) < 6) {
            $_SESSION['erro'] = "A senha deve conter no mínimo 6 caracteres.";
            return false;
        }
        return true;
    }

    /** FORMATA NOME */
    private function formatarNome($nome)
    {
        return ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $nome))));
    }

    /** LOG DE ERROS SIMPLES */
    private function logErro($metodo, $mensagem)
    {
        $logFile = __DIR__ . '/../../logs/usuarios.log';
        $mensagemFormatada = "[" . date('Y-m-d H:i:s') . "] {$metodo}: " . (is_array($mensagem) ? json_encode($mensagem) : $mensagem) . PHP_EOL;
        file_put_contents($logFile, $mensagemFormatada, FILE_APPEND);
    }
}
