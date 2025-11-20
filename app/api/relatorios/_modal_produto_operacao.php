<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();

// -----------------------------
// VALIDAR PARÂMETROS
// -----------------------------

$produto_id = (int) $_GET['produto_id'];
$operacao   = (int) $_GET['operacao'];

try {

    if ($operacao === 0) {
        // =====================================================
        // 🔹 SAÍDAS (operacao = 0)
        // =====================================================

        $sql = "
            SELECT 
                p.produto AS produto_nome,
                u.user_name AS funcionario_nome,
                s.cad_data,
                s.quantidade
            FROM est_saidas s
            INNER JOIN produtos p ON p.produto_id = s.produto_id
            INNER JOIN funcionarios u ON u.user_id = s.funcionario_id
            WHERE s.produto_id = :produto_id
            ORDER BY s.cad_data DESC
            LIMIT 10
        ";
    } else {
        // =====================================================
        // 🔹 ENTRADAS (operacao = 1)
        // =====================================================

        $sql = "
            SELECT 
                p.produto AS produto_nome,
                f.fornecedor_razao AS fornecedor_nome,
                e.cad_data,
                e.quantidade,
                e.preco
            FROM est_entradas e
            INNER JOIN produtos p ON p.produto_id = e.produto_id
            INNER JOIN fornecedor f ON f.fornecedor_id = e.fornecedor_id
            WHERE e.produto_id = :produto_id
            ORDER BY e.cad_data DESC
            LIMIT 10
        ";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['produto_id' => $produto_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
