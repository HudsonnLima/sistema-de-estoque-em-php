<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

unset($_SESSION['flash_message']);

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) $data = $_POST;

if (empty($data['user_id'])) {
    $msg = 'ID do usuário não informado.';
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => $msg];
    echo json_encode(['status' => false, 'mensagem' => $msg]);
    exit;
}

try {

    // Verifica se existe o usuário
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE user_id = :user_id LIMIT 1");
    $stmt->bindParam(':user_id', $data['user_id']);
    $stmt->execute();

    if (!$stmt->fetchColumn()) {
        $msg = 'Usuário não encontrado.';
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => $msg];
        echo json_encode(['status' => false, 'mensagem' => $msg]);
        exit;
    }

    // Executa delete
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $data['user_id']);
    $stmt->execute();

    $deleted = $stmt->rowCount();
    $msg = 'Usuário excluído com sucesso! (' . $deleted . ' registro(s) removido(s))';
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => $msg];

    echo json_encode(['status' => true, 'mensagem' => $msg]);

} catch (Exception $e) {
    $msg = 'Erro ao excluir: ' . $e->getMessage();
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => $msg];
    echo json_encode(['status' => false, 'mensagem' => $msg]);
}
