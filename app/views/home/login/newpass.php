<?php
require_once BASE_CONF . '/database.php';

$db = new Database();
$pdo = $db->getConnection();


// Verifica se o ID da URL bate com o ID da sessão
$urlUserId = $_GET['user_id'] ?? null;
$sessionUserId = $_SESSION['user_id'] ?? null;

if ((int)$urlUserId !== (int)$sessionUserId || !isset($sessionUserId)) {
    $_SESSION['erro'] = "Acesso negado.";
    header("Location: " . BASE_LOGIN);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RecoverPass'])) {
    $newPassword = $_POST['new_password'] ?? '';
    $newPasswordCheck = $_POST['new_password_check'] ?? '';

    if (empty($newPassword)) {
        $_SESSION['erro'] = "A nova senha não pode ser vazia.";
        header("Location: " . BASE_LOGIN . "/newpass?user_id=" . urlencode($urlUserId));
        exit;
    }

    if ($newPassword !== $newPasswordCheck) {
        $_SESSION['erro'] = "As senhas não coincidem.";
        header("Location: " . BASE_LOGIN . "/newpass?user_id=" . urlencode($urlUserId));
        exit;
    }

    $email = $_SESSION['user_mail'] ?? '';
    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

    try {
        // Evita update se a nova senha for igual à anterior
        $stmtCheck = $pdo->prepare("SELECT user_pass FROM users WHERE user_id = :id AND user_mail = :email");
        $stmtCheck->bindValue(':id', $urlUserId, PDO::PARAM_INT);
        $stmtCheck->bindValue(':email', $email);
        $stmtCheck->execute();

        $currentHash = $stmtCheck->fetchColumn();

        if ($currentHash && password_verify($newPassword, $currentHash)) {
            $_SESSION['erro'] = "A nova senha não pode ser igual à anterior.";
            header("Location: " . BASE_LOGIN . "/newpass?user_id=" . urlencode($urlUserId));
            exit;
        }
        
        // Atualiza a senha SEM depender de rowCount
        $stmt = $pdo->prepare("UPDATE users 
        SET user_pass = :new_password, user_lastupdate = NOW() 
        WHERE user_id = :id AND user_mail = :email");

        $stmt->bindValue(':new_password', $newPasswordHash);
        $stmt->bindValue(':id', $urlUserId, PDO::PARAM_INT);
        $stmt->bindValue(':email', $email);

        try {
            $executou = $stmt->execute();

            if ($executou) {
                $_SESSION['sucesso'] = "Senha modificada com sucesso!";
            } else {
                $_SESSION['erro'] = "Falha ao executar o update.";
            }

            header("Location: " . BASE_LOGIN);
            exit;
        } catch (PDOException $e) {
            $_SESSION['erro'] = "Erro no banco: " . $e->getMessage();
            header("Location: " . BASE_LOGIN . "/newpass?user_id=" . urlencode($urlUserId));
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao atualizar a senha.";
        header("Location: " . BASE_LOGIN . "/newpass?user_id=" . urlencode($urlUserId));
        exit;
    }
}
?>


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