<?php
// app/api/buscarCompra.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

/*
require_once __DIR__ . '/../Core/Database.php';

$db = new Database();
$pdo = $db->getConnection();
*/

try {
    // Somente POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['sucesso' => false, 'error' => 'Método não permitido']);
        exit;
    }

    // Validação básica
    if (!isset($_POST['compra_id']) || !is_numeric($_POST['compra_id'])) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'error' => 'compra_id inválido']);
        exit;
    }

    $id = (int) $_POST['compra_id'];

    $sql = "SELECT c.compra_id, c.pagamento_id, c.cad_data, c.previsao,
                   f.fornecedor_razao AS fornecedor,
                   u.user_name AS usuario
            FROM compras c
            LEFT JOIN fornecedor f ON f.fornecedor_id = c.fornecedor_id
            LEFT JOIN users u ON u.user_id = c.cad_autor
            WHERE c.compra_id = :id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $compra = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($compra) {
        echo json_encode([
            'sucesso'   => true,
            'fornecedor'=> $compra['fornecedor'] ?? '',
            'pagamento' => $compra['pagamento_id'] ?? '',
            'usuario'   => $compra['usuario'] ?? '',
            'data'      => isset($compra['cad_data']) ? date('Y-m-d', strtotime($compra['cad_data'])) : '',
            'previsao'  => isset($compra['previsao']) ? date('Y-m-d', strtotime($compra['previsao'])) : ''
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'error' => 'Compra não encontrada']);
    }
    exit;
} catch (Exception $e) {
    // Em desenvolvimento: retornar mensagem para debug. Em produção, logue e retorne genérico.
    http_response_code(500);
    // opcional: gravar no log de erros
    error_log('buscarCompra ERROR: ' . $e->getMessage());
    echo json_encode(['sucesso' => false, 'error' => 'Erro interno no servidor']);
    exit;
}
