<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

header('Content-Type: application/json');

if (!isset($_GET['fornecedor_id'])) {
    echo json_encode(['error' => 'Fornecedor ID não informado']);
    exit;
}

$fornecedor_id = (int) $_GET['fornecedor_id'];

try {
    // Pega os últimos 10 registros da tabela est_entradas por fornecedor
    $sql = "
        SELECT 
            f.fornecedor_razao,
            e.cad_data AS data_compra,
            e.ent_data AS data_entrada,
            SUM(ee.quantidade * ee.preco) AS valor_total
        FROM est_entradas e
        INNER JOIN est_entradas ee ON e.compra_id = ee.compra_id
        INNER JOIN fornecedor f ON f.fornecedor_id = e.fornecedor_id
        WHERE e.fornecedor_id = :fornecedor_id
        GROUP BY e.compra_id
        ORDER BY e.cad_data DESC
        LIMIT 10
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['fornecedor_id' => $fornecedor_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
