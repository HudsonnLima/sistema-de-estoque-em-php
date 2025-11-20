<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

try {
    $stmt = $pdo->prepare("
        SELECT 
    f.user_name,
    SUM(e.quantidade) AS total_saidas
FROM est_saidas e
JOIN funcionarios f ON f.user_id = e.funcionario_id
WHERE e.cad_data >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND f.empresa_id = 1
GROUP BY e.funcionario_id
ORDER BY total_saidas DESC
LIMIT 6;
    ");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monta arrays separados para Chart.js
    $labels = [];
    $valores = [];
    foreach ($result as $row) {
        $labels[] = $row['user_name'];
        $valores[] = (float)$row['total_saidas'];
    }

    echo json_encode([
        'labels' => $labels,
        'valores' => $valores
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'labels' => [],
        'valores' => [],
        'error' => $e->getMessage()
    ]);
}
