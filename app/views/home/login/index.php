<?php
use App\Core\Database;
use App\Models\ModelLogin;
use App\Controllers\ControllerLogin;

$db = new Database();
$pdo = $db->getConnection();

$modelLogin = new ModelLogin($pdo);
$controller = new ControllerLogin($modelLogin);

$loginData = $controller->processLogin();

$userEmail = $loginData['userEmail'];
$userId = $loginData['userId'];
$error = $loginData['error'];
?>


<form name="AdminLoginForm" class="main-login" method="POST">
    <div class="login">

        <div class="card-login">
            <?php
            if (isset($_SESSION['sucesso'])) {
                echo '<div class="trigger accept">' . $_SESSION['sucesso'] . '</div>';
                unset($_SESSION['sucesso']);
            }

            if (isset($_SESSION['erro'])) {
                echo '<div class="trigger error">' . $_SESSION['erro'] . '</div>';
                unset($_SESSION['erro']);
            }
            ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="form-floating mb-3">
                <input name="user_name" type="text" class="form-control" id="floatingInput" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                <label for="floatingInput">Usuário:</label>
            </div>

            <div class="form-floating">
                <input name="user_pass" type="password" class="form-control" id="floatingPassword" required>
                <label for="floatingPassword">Senha:</label>
            </div>

            <div class="option-login">
                <div class="remember">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="remember" <?php echo $userEmail ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="flexSwitchCheckChecked">Lembrar Usuário</label>
                    </div>
                </div>

                <div class="recover">
                    <a href="<?= BASE_REMEMBER; ?>">Esqueci minha senha</a>
                </div>
            </div>

            <input type="submit" name="AdminLogin" value="Acessar" class="btn-login" />

            <div class="autor">
                <h1>Desenvolvido por <a href="https://www.gnrsystem.com.br" target="_blank">GNR SYSTEM</a></h1>
                <h2>Entre em contato: <a href="https://api.whatsapp.com/send?phone=5524992380747" target="_blank">Whatsapp</a></h2>
            </div>
        </div>
    </div>
</form>