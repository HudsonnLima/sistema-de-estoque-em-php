<?php

require_once BASE_CONF . '/database.php';

class Login
{
    private $pdo;
    private $error;
    private $result;
    private $email;
    private $username;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function authenticate($emailOrUsername, $password, $remember = false)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_email = :emailOrUsername OR user_name = :emailOrUsername");
            $stmt->bindParam(':emailOrUsername', $emailOrUsername, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['user_pass'])) {
                $this->result = $user;
                $this->email = $user['user_email'];
                $this->username = $user['user_name'];

                $this->execute($remember);
                return true;
            }

            return false;
        } catch (PDOException $e) {
            die("Erro ao autenticar: " . $e->getMessage());
        }
    }

    private function execute($remember)
    {
        if (!session_id()) {
            session_start();
        }

        $_SESSION['user_id'] = $this->result['user_id'];
        $_SESSION['user_name'] = $this->result['user_name'];
        $_SESSION['user_email'] = $this->result['user_email'];
        $_SESSION['user_function_id'] = $this->result['user_function_id'];

        $_SESSION['userlogin'] = $this->result;

        if ($remember) {
            // Define cookies para 30 dias
            setcookie('user_email', $this->email, time() + (30 * 24 * 60 * 60), "/");
            setcookie('user_name', $this->username, time() + (30 * 24 * 60 * 60), "/");
        } else {
            // Apaga cookies se não lembrar
            setcookie('user_email', '', time() - 3600, "/");
            setcookie('user_name', '', time() - 3600, "/");
        }

        $this->error = ["Olá {$this->result['user_name']}, seja bem-vindo(a). Aguarde Redirecionamento!"];
    }

    public function getError()
    {
        return $this->error;
    }

    public function getResult()
    {
        return $this->result;
    }
}
