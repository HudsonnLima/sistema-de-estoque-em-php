<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../vendor/autoload.php';
use App\Core\Database;
$db = new Database();
$pdo = $db->getConnection();

/*
require_once __DIR__ . '/../Core/Database.php';
*/

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['sucesso' => false, 'error' => 'Método não permitido']);
        exit;
    }

    if (!isset($_POST['produto_id']) || !is_numeric($_POST['produto_id'])) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'error' => 'produto_id inválido']);
        exit;
    }

    $id = (int) $_POST['produto_id'];

    $sql = "DELETE FROM compras WHERE produto_id = :id"; // ajuste para sua tabela
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount()) {
        $_SESSION['success'] = 'Produto excluído com sucesso!';
        echo json_encode(['sucesso' => true]);
    } else {
        $_SESSION['error'] = 'Produto não encontrado ou já excluído';
        echo json_encode(['sucesso' => false]);
    }
    exit;

} catch (Exception $e) {
    http_response_code(500);
    error_log('excluirProduto ERROR: ' . $e->getMessage());
    $_SESSION['error'] = 'Erro interno no servidor';
    echo json_encode(['sucesso' => false]);
    exit;
}
