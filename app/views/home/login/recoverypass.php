<?php


use App\Core\Database;
$db = new Database();
$pdo = $db->getConnection();

$token = $_GET['token'] ?? null;

if (!$token) {
    $_SESSION['erro'] = "Token de recuperação inválido.";
    header("Location: " . BASE_LOGIN);
    exit;
}

// Verifica se o token é válido e ainda não expirou
$stmt = $pdo->prepare("SELECT user_rec_mail FROM user_pass_reset WHERE user_rec_token = :token AND user_rec_expires_at > NOW()");
$stmt->bindParam(':token', $token);
$stmt->execute();

$email = $stmt->fetchColumn();

if (!$email) {
    $_SESSION['erro'] = "Link expirado ou inválido.";
    header("Location: " . BASE_LOGIN);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RecoverPass'])) {
    $newPassword = $_POST['new_password'] ?? '';
    $newPasswordCheck = $_POST['new_password_check'] ?? '';

    if (empty($newPassword)) {
        $_SESSION['erro'] = "A nova senha senha não pode ser vazia.";
        header("Location: " . BASE_LOGIN . "/recoverypass?token=" . urlencode($token));
        exit;
    }

    if ($newPassword !== $newPasswordCheck) {
        $_SESSION['erro'] = "As senhas não coincidem.";
        header("Location: " . BASE_LOGIN . "/recoverypass?token=" . urlencode($token));
        exit;
    }

    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

    // Atualiza a senha do usuario
    $stmt = $pdo->prepare("UPDATE users SET user_pass = :new_password, user_lastupdate = NOW() WHERE user_email = :email");
    $stmt->bindParam(':new_password', $newPasswordHash);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Remove o token apos o uso
    $stmt = $pdo->prepare("DELETE FROM user_pass_reset WHERE user_rec_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    $_SESSION['sucesso'] = "Senha modificada com sucesso!";
    header("Location: " . BASE_LOGIN);
    exit;
}
?>

<!-- Formulario de Nova Senha -->
<form name="AdminLoginForm" class="main-login" method="POST">
    <div class="login">
        <div class="card-login">

            <?php
            if (isset($_SESSION['erro'])) {
                echo '<div class="trigger error">' . $_SESSION['erro'] . '</div>';
                unset($_SESSION['erro']);
            }
            if (isset($_SESSION['sucesso'])) {
                echo '<div class="trigger accept">' . $_SESSION['sucesso'] . '</div>';
                unset($_SESSION['sucesso']);
            }
            ?>

            <div class="form-floating mb-3">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input name="new_password" type="password" id="pass1" class="form-control" required>
                <label for="pass1">Nova Senha:</label>
            </div>
            <div class="form-floating mb-3">
                <input name="new_password_check" type="password" id="pass2" class="form-control" required>
                <label for="pass2">Repita a senha:</label>
            </div>

            <input type="submit" name="RecoverPass" id="recoverBtn" value="Alterar Senha" class="btn-login" />

            <div class="autor">
                <h1>Desenvolvido por <a href="http://www.dinholima.com" target="_blank">Hudson Lima</a></h1>
                <h2>Entre em contato: <a href="https://api.whatsapp.com/send?phone=5524992380747" target="_blank">Whatsapp</a></h2>
            </div>
        </div>
    </div>
</form>

<script src="<?= BASEJS; ?>/password.js"></script>
