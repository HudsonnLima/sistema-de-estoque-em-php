<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

header('Content-Type: application/json; charset=utf-8');

$grupo_id = isset($_GET['grupo_id']) ? (int)$_GET['grupo_id'] : 0;

if ($grupo_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT setor_id, setor_nome FROM setor WHERE setor_grupo_id = :grupo_id ORDER BY setor_nome ASC");
    $stmt->execute([':grupo_id' => $grupo_id]);
    $setores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($setores);
} catch (Exception $e) {
    echo json_encode([]);
}
exit;
