<?php

namespace App\Models;
use App\Core\Database;

use App\Helpers\PHPMailerHelper;

class PasswordRecovery
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function recoveryMail($email): array
    {
        $retorno = [];

        if (empty($email)) {
            $retorno['alert'] = '<div class="alert alert-danger">Por favor, insira um e-mail válido.</div>';
            return $retorno;
        }

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Verifica se o e-mail existe
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if (!$user) {
            $retorno['naoencontrado'] = '<div class="alert alert-primary ">E-mail não encontrado em nossos registros.</div>';
            return $retorno;
        }

        // Deleta tokens antigos para esse e-mail
        $stmt = $this->pdo->prepare("DELETE FROM user_pass_reset WHERE user_rec_mail = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Gera o token e define tempo de expiração (1 hora, por exemplo)
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Salva na tabela user_pass_reset
        $stmt = $this->pdo->prepare("INSERT INTO user_pass_reset (user_rec_mail, user_rec_token, user_rec_expires_at) VALUES (:email, :token, :expires)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expiresAt);
        $stmt->execute();


        // Link com token
        $link =  BASE_URL . "/login/recoverypass?token=" . urlencode($token);

        $mailer = new PHPMailerHelper($this->pdo);
        $enviado = $mailer->send(
            $email,
            $user['user_name'] ?? 'Usuário',
            'Recuperação de Senha',
            "<p>Olá {$user['user_name']},<br> Clique no link abaixo para redefinir sua senha:<br><a href=\"{$link}\">Recuperar Senha</a><br>Este link expira em 1 hora.</p>"
        );

        if ($enviado) {
            $_SESSION['sucesso'] = 'E-mail enviado com sucesso!<br>Verifique sua caixa de entrada.';
            header("Location: " . BASE_LOGIN);
            exit;
        } else {
            $retorno['error'] = '<div class="trigger error">Falha ao enviar e-mail.</div>';
        }

        return $retorno;
    }
}
