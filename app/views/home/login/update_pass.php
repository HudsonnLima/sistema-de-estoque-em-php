<?php
ob_start();
session_start();
require '../../_app/conf.php';
require '../../_app/Config.inc.php'; 
date_default_timezone_set('America/Sao_Paulo');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = md5($_POST['new_password']);

    // Verifica se o token é válido e não expirou
    $stmt = $pdo->prepare("SELECT email FROM user_pass_reset WHERE token = :token AND expires_at > NOW()");
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $email = $stmt->fetchColumn();

        // Atualiza a senha do usuário
        $currentDateTime = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("UPDATE users SET user_password = :new_password, user_lastupdate = :user_lastupdate WHERE user_email = :email");
        $stmt->bindParam(':new_password', $new_password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_lastupdate', $currentDateTime);
        $stmt->execute();

        // Remove o token usado
        $stmt = $pdo->prepare("DELETE FROM user_pass_reset WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $_SESSION['msg'] = '<div class="trigger accept">Senha modificada com sucesso!</div>';
        header("Location:  ".BASE."admin/");
    } else {
        echo '';
        $_SESSION['msg'] = '<div class="trigger error">Token inválido ou expirado!</div>';
        header("Location:  ".BASE."admin/remember/");
    }
}
?>
