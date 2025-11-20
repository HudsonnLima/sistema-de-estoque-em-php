<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

try {
    $stmt = $pdo->prepare("
SELECT 
    f.fornecedor_id,
    f.fornecedor_fantasia,
    (e.quantidade * e.preco) AS total_compras
FROM est_entradas e
JOIN fornecedor f ON f.fornecedor_id = e.fornecedor_id
JOIN (
    -- Subquery: última compra de cada fornecedor
    SELECT fornecedor_id, MAX(cad_data) AS ultima_compra
    FROM est_entradas
    WHERE cad_data >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY fornecedor_id
) ult ON e.fornecedor_id = ult.fornecedor_id AND e.cad_data = ult.ultima_compra
ORDER BY total_compras DESC
LIMIT 6;

    ");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $result
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
