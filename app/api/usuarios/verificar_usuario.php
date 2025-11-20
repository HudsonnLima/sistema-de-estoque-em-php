<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

$pdo = (new Database())->getConnection();

$campo = $_POST['campo'] ?? '';
$valor = $_POST['valor'] ?? '';
$currentId = $_POST['user_id'] ?? null; // ID do usuário que está sendo editado

$resposta = ['existe' => false];

if ($campo && $valor) {
    if ($currentId) {
        // Ignora o próprio registro durante a edição
        $sql = "SELECT COUNT(*) FROM users WHERE $campo = :valor AND user_id != :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['valor' => $valor, 'id' => $currentId]);
    } else {
        $sql = "SELECT COUNT(*) FROM users WHERE $campo = :valor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['valor' => $valor]);
    }
    
    $count = $stmt->fetchColumn();
    $resposta['existe'] = $count > 0;
}

echo json_encode($resposta);
