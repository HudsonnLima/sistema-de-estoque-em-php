<?php
ob_start();
session_start();

require '../../_app/conf.php';
require '../../_app/Config.inc.php'; // Inclua sua conexão ao banco de dados

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../_app/Library/PHPMailer/src/Exception.php';
require '../../_app/Library/PHPMailer/src/PHPMailer.php';
require '../../_app/Library/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        // Verifica se o email existe no banco de dados

        $stmt = "SELECT user_id FROM users WHERE user_email = :email";
        $ckeck_mail = $pdo->prepare($stmt);
        $ckeck_mail->bindParam(':email', $email);
        $ckeck_mail->execute();



        if ($ckeck_mail->rowCount() > 0) {
            // Gera um token único
            $token = bin2hex(random_bytes(16));
            $link = BASE . 'admin/remember/index2.php?token=' . $token;

            // Salva o token no banco de dados com uma validade de 1 hora
            $stmt = $pdo->prepare("INSERT INTO user_pass_reset (email, token, expires_at) VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            $config = "SELECT * FROM config WHERE id = 1";
            $conf = $pdo->prepare($config);
            $conf->execute();
            $mailler = $conf->fetch(PDO::FETCH_ASSOC);

            // Configurações de envio do email.
            $mail->isSMTP();        //Devine o uso de SMTP no envio
            $mail->SMTPAuth = true; //Habilita a autenticação SMTP
            $mail->Username   = $mailler['email'];
            $mail->Password   = $mailler['pass'];
            $mail->SMTPSecure = 'tls';
            $mail->Host = $mailler['servidor'];
            $mail->Port = $mailler['port'];
            $mail->setFrom($mailler['email'], $mailler['site_name']); // Define o remetente
            $mail->addAddress($mailler['receiver_mail'], 'Sistema'); // Define o destinatário
            // Conteúdo da mensagem
            $mail->isHTML(true);  // Seta o formato do e-mail para aceitar conteúdo HTML
            $mail->Subject = "Recuperação de senha";
            $mail->Body = "Clique no link para redefinir sua senha:<br/> <a href='$link'>Redefinir Senha</a>";
            $mail->AltBody = "Clique no link para redefinir sua senha:<br/> <a href='$link'>Redefinir Senha</a>";
            $mail->send();

            $_SESSION['msg'] = '<div class="trigger accept">Verifique sua caixa de email!</div>';
            header("Location:  " . BASE . "admin/");

        } else {
            $_SESSION['msg'] = '<div class="trigger alert">Verifique o email digitado e tente novamente!</div>';
            header("Location:  " . BASE . "admin/remember/");
        }
    } else {
        $_SESSION['msg'] = '<div class="trigger error">O email digitado, não é válido!</div>';
        header("Location:  " . BASE . "admin/remember/");
    }
}
