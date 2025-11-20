<?php
ob_start();
require_once dirname(__DIR__, 4) . '/vendor/autoload.php';
require_once BASE_CONF . '/database.php';


use App\Models\PasswordRecovery;

// Instancia a conexão
$db = new \Database();
$pdo = $db->getConnection();

$passwordRecovery = new PasswordRecovery($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['AdminLogin'])) {
    $retorno = $passwordRecovery->recoveryMail($_POST['email'] ?? '');

    foreach ($retorno as $key => $msg) {
        $_SESSION[$key] = $msg;
    }

    // Redireciona para a própria página explicitamente
    header('Location:' . BASE_REMEMBER);
    exit;
}

?>


<form name="AdminLoginForm" class="main-login" method="POST">

<?php


    ?>
    <div class="login">

        <div class="card-login">
            <?php
            if (isset($_SESSION['sucesso'])) {
                echo $_SESSION['sucesso'];
                unset($_SESSION['sucesso']);
                
            }

            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            }

            if (isset($_SESSION['alert'])) {
                echo $_SESSION['alert'];
                unset($_SESSION['alert']);
            }
            if (isset($_SESSION['naoencontrado'])) {
                echo $_SESSION['naoencontrado'];
                unset($_SESSION['naoencontrado']);
            }

            ?>


            <div class="form-floating mb-3">
                <input name="email" type="text" class="form-control" id="floatingInput" value="">
                <label for="floatingInput">Email:</label>
            </div>
            <div class="option-login">
                <div class="recover"><a href="<?= BASE_LOGIN ?>">Fazer Login</a></div>
            </div>
            <input type="submit" name="AdminLogin" value="Enviar" class="btn-login" />

            <div class="autor">
                <h1>Desenvolvido por <a href="https://www.gnrsystem.com.br" target="_blank">GNR SYSTEM</a></h1>
                <h2>Entre em contato: <a href="https://api.whatsapp.com/send?phone=5524992380747" target="_blank">Whatsapp</a></h2>
            </div>
        </div>
    </div>
</form>


<?php
ob_end_flush();
?>