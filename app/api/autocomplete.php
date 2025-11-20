<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

header('Content-Type: application/json');

if (isset($_POST['busca'])) {
    $produto = '%' . $_POST['busca'] . '%';

    $sql = "SELECT produto_id, produto, codigo, estoque, medida_id, preco 
            FROM produtos 
            WHERE produto LIKE :produto 
              AND produto_status = 1 
            ORDER BY produto ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':produto', $produto);
    $stmt->execute();

    $result = [];

    while ($prod = $stmt->fetch(PDO::FETCH_OBJ)) {
        $result[] = (object)[
            "label" => $prod->produto,
            "produto_id" => $prod->produto_id,
            "codigo" => $prod->codigo,
            "estoque" => $prod->estoque,
            "medida_id" => $prod->medida_id,
            "preco" => $prod->preco
        ];
    }

    // Caso não encontre produtos
    if (empty($result)) {
        $result[] = (object)[
            "label" => "Nenhum produto cadastrado encontrado.",
            "produto_id" => null
        ];
    }

    echo json_encode($result);
}
exit;
