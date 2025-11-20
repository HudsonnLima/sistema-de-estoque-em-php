<?php
namespace App\Controllers;

use App\Models\ModelLogin;

class ControllerLogin
{
    private $modelLogin;
    private $error;

    public function __construct(ModelLogin $modelLogin)
    {
        $this->modelLogin = $modelLogin;
    }

    public function processLogin()
{
    if (!session_id()) {
        session_start();
    }

    $userEmail = $_COOKIE['user_email'] ?? '';
    $userId = $_SESSION['user_id'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['AdminLogin'])) {
        $username = trim($_POST['user_name']);
        $password = trim($_POST['user_pass']);
        $remember = isset($_POST['remember']);

        $user = $this->modelLogin->findUser($username);

        if ($user && password_verify($password, $user['user_pass'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_function_id'] = $user['user_function_id'];

            if ($remember) {
                setcookie('user_email', $user['user_email'], time() + (30 * 24 * 60 * 60), "/");
                setcookie('user_name', $user['user_name'], time() + (30 * 24 * 60 * 60), "/");
            } else {
                setcookie('user_email', '', time() - 3600, "/");
                setcookie('user_name', '', time() - 3600, "/");
            }

            header('Location: ' . BASE_URL . '/home');
            exit;

        } else {
            $_SESSION['erro'] = "Email ou senha inválido, tente novamente.";
        }
    }

    return [
        'userEmail' => $userEmail,
        'userId' => $userId,
        // remove error daqui
    ];
}

}
