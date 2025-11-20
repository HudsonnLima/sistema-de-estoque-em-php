<?php
// PÁGINA DE TESTE DE ENVIO DE EMAIL
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE . '/app/Core/Database.php';
require_once BASE . '/vendor/autoload.php';

use App\Core\Database;
$db = new Database();
$pdo = $db->getConnection();

use App\Models\EmailCompras;
$model = new EmailCompras($pdo);

// Dados de teste

$stmt = $pdo->prepare("SELECT servidor, receiver_mail, pass, port FROM config LIMIT 1");
$stmt->execute();
$data = $stmt->fetch(\PDO::FETCH_ASSOC);


//$email = 'hudsonnasclima@gmail.com';
$email = $data['receiver_mail'];
$nome = 'Hudson';

// Enviar email
$enviado = $model->sendConfirmationToUser($email, $nome);

if ($enviado) {
    echo "E-mail enviado com sucesso!";
    echo '<br/>'.$data['receiver_mail'];
} else {
    echo "Erro ao enviar e-mail.";
}
