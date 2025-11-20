<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

header('Content-Type: application/json');

try {
    // pega params POST
    $cnpj_raw = isset($_POST['fornecedor_cnpj']) ? $_POST['fornecedor_cnpj'] : '';
    $fornecedor_atual = isset($_POST['fornecedor_id']) ? (int)$_POST['fornecedor_id'] : 0;

    // normaliza o cnpj: só dígitos
    $cnpj = preg_replace('/\D/', '', $cnpj_raw);

    if (!$cnpj) {
        echo json_encode(['error' => 'CNPJ inválido']);
        exit;
    }

    // Supondo que no banco o campo fornecedor_cnpj esteja armazenado sem máscara.
    // Caso esteja com máscara, remova o preg_replace na query e ajuste conforme necessário.
    $stmt = $pdo->prepare("SELECT fornecedor_id FROM fornecedor WHERE REPLACE(REPLACE(REPLACE(REPLACE(fornecedor_cnpj, '.', ''), '/', ''), '-', ''), ' ', '') = :cnpj LIMIT 1");
    $stmt->bindValue(':cnpj', $cnpj, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // se pertence a outro fornecedor -> exists true
        if ((int)$result['fornecedor_id'] !== $fornecedor_atual) {
            echo json_encode([
                'exists' => true,
                'fornecedor_id' => (int)$result['fornecedor_id']
            ]);
        } else {
            // pertence ao mesmo -> livre
            echo json_encode(['exists' => false]);
        }
    } else {
        // não encontrado -> livre
        echo json_encode(['exists' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
