<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Database;

$db = new Database();
$pdo = $db->getConnection();

// Captura os últimos 6 meses (mês atual + 5 anteriores)
$meses = [];
for ($i = 5; $i >= 0; $i--) {
    $meses[] = date('Y-m', strtotime("-$i months"));
}

// Monta arrays padrão (zera meses sem movimento)
$dados = [
    'labels' => [],
    'entradas' => [],
    'saidas' => []
];

// Pega Entradas
$sqlEntradas = "
    SELECT DATE_FORMAT(cad_data, '%Y-%m') AS mes, SUM(quantidade) AS total
    FROM est_entradas
    WHERE operacao = 1
      AND cad_data >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY mes
";
$stmt = $pdo->query($sqlEntradas);
$entradas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Pega Saídas
$sqlSaidas = "
    SELECT DATE_FORMAT(cad_data, '%Y-%m') AS mes, SUM(quantidade) AS total
    FROM est_saidas
    WHERE operacao = 0
      AND cad_data >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY mes
";
$stmt = $pdo->query($sqlSaidas);
$saidas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Formata os meses em PT-BR
$formatter = new IntlDateFormatter(
    'pt_BR', 
    IntlDateFormatter::SHORT, 
    IntlDateFormatter::NONE,
    'America/Sao_Paulo',
    IntlDateFormatter::GREGORIAN,
    'MMM/yy'
);

// Monta o resultado final
foreach ($meses as $mes) {
    $date = new DateTime($mes . '-01');
    $dados['labels'][] = ucfirst($formatter->format($date));

    // Adiciona os valores conforme o mês (ou 0 se não houver)
    $dados['entradas'][] = isset($entradas[$mes]) ? (float)$entradas[$mes] : 0;
    $dados['saidas'][]   = isset($saidas[$mes]) ? (float)$saidas[$mes] : 0;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($dados, JSON_UNESCAPED_UNICODE);
