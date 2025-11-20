<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

/*
require_once '../config/database.php';
*/

unset($_SESSION['flash_message']);

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) $data = $_POST;

if (empty($data['compra_id'])) {
    $msg = 'ID da compra não informado.';
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => $msg];
    echo json_encode(['status' => false, 'mensagem' => $msg]);
    exit;
}

try {
    // Verifica se existe a compra
    $stmt = $pdo->prepare("SELECT 1 FROM compras WHERE compra_id = :id LIMIT 1");
    $stmt->bindParam(':id', $data['compra_id']);
    $stmt->execute();

    if (!$stmt->fetchColumn()) {
        $msg = 'Compra não encontrada.';
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => $msg];
        echo json_encode(['status' => false, 'mensagem' => $msg]);
        exit;
    }

    // Executa delete
    $stmt = $pdo->prepare("DELETE FROM compras WHERE compra_id = :id");
    $stmt->bindParam(':id', $data['compra_id']);
    $stmt->execute();

    $deleted = $stmt->rowCount();
    $msg = 'Compra excluída com sucesso! (' . $deleted . ' registro(s) removido(s))';
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => $msg];

    echo json_encode(['status' => true, 'mensagem' => $msg]);

} catch (Exception $e) {
    $msg = 'Erro ao excluir: ' . $e->getMessage();
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => $msg];
    echo json_encode(['status' => false, 'mensagem' => $msg]);
}
