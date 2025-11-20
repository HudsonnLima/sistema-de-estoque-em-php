<?php

use App\Core\Database;
use App\Models\Read;

$read = new Read();
$limite = 50;

// Página atual
$paginaAtual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $limite;

// Coleta filtros
$fornecedor = trim($_GET['fornecedor'] ?? '');
$startDate  = trim($_GET['startDate'] ?? '');
$endDate    = trim($_GET['endDate'] ?? '');
$params     = [];
$where      = "WHERE c.estado = 1";

// Filtros
if ($fornecedor !== '') {
    $where .= " AND f.fornecedor_razao LIKE ?";
    $params[] = "%{$fornecedor}%";
}
if ($startDate !== '' && $endDate !== '') {
    $where .= " AND c.cad_data BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
} elseif ($startDate !== '') {
    $where .= " AND c.cad_data >= ?";
    $params[] = $startDate;
} elseif ($endDate !== '') {
    $where .= " AND c.cad_data <= ?";
    $params[] = $endDate;
}
// Conta total
$countQuery = "SELECT COUNT(*) AS total
               FROM compras c
               INNER JOIN fornecedor f ON c.fornecedor_id = f.fornecedor_id
               $where";

$totalResultados = $read->fetch($countQuery, $params)['total'] ?? 0;
$totalPaginas = ceil($totalResultados / $limite);

$query = "SELECT 
            c.compra_id,
            c.fornecedor_id,
            c.cad_autor,
            c.cad_data,
            c.ent_data,
            f.fornecedor_razao
          FROM compras c
          LEFT JOIN fornecedor f 
                ON c.fornecedor_id = f.fornecedor_id
          $where
          GROUP BY 
            c.compra_id,
            c.fornecedor_id,
            c.cad_autor,
            c.cad_data,
            c.ent_data,
            f.fornecedor_razao
          ORDER BY c.cad_data DESC
          LIMIT $limite OFFSET $offset";




$compras = $read->fetchAll($query, $params);

?>

<label class="titulo">Compras finalizadas:</label>
<hr>

<form id="buscaprodutos" method="get">
    <div class="row g-2">
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor"
                    value="<?= htmlspecialchars($fornecedor, ENT_QUOTES) ?>">
                <label>Pesquisar pelo Fornecedor:</label>
            </div>
        </div>
        <div class="form-group col-md-2">
            <div class="form-floating">
                <input type="date" class="form-control" name="startDate"
                    value="<?= htmlspecialchars($startDate, ENT_QUOTES) ?>">
                <label>Data Inicial</label>
            </div>
        </div>
        <div class="form-group col-md-2">
            <div class="form-floating">
                <input type="date" class="form-control" name="endDate"
                    value="<?= htmlspecialchars($endDate, ENT_QUOTES) ?>">
                <label>Data Final</label>
            </div>
        </div>
        <div class="form-group col-md-2 d-flex align-items-center gap-2">
            <button type="submit" class="btn-submit w-100">Buscar</button>
            <button type="button" id="limparFiltros" class="btn-clean w-100">Limpar</button>
        </div>
    </div>
</form>

<script>
// envia apenas campos preenchidos
document.getElementById('buscaprodutos').addEventListener('submit', function(e){
    e.preventDefault();
    const form = e.target;
    const params = new URLSearchParams();
    for (const el of form.elements) {
        if (el.name && el.type !== 'submit' && el.type !== 'button' && el.value.trim() !== '') {
            params.append(el.name, el.value.trim());
        }
    }
    window.location.href = `${window.location.pathname}?${params.toString()}`;
});
document.getElementById('limparFiltros').addEventListener('click', () => {
    window.location.href = window.location.pathname;
});
</script>

<?php if ($totalResultados > 0): ?>
    <div class="table-responsive mt-3">
        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th>Fornecedor</th>
                    <th>NFe</th>
                    <th>Comprador</th>
                    <th>R$ - Total</th>
                    <th>Data da compra</th>
                    <th>Data da chegada</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($compras as $dados): ?>
                <?php
                    $compra_id = $dados['compra_id'];

                    // Busca num_nfe direto
                    $nfe = $read->fetch("SELECT num_nfe FROM est_entradas WHERE compra_id = ?", [$compra_id]);
                    $num_nfe = $nfe['num_nfe'] ?? 'Não cadastrada';

                    // Comprador
                    $usr = $read->fetch("SELECT user_name FROM users WHERE user_id = ?", [$dados['cad_autor']]);
                    $comprador = $usr['user_name'] ?? 'Usuário não encontrado';

                    // Total
                    $soma = $pdo->query("SELECT SUM(quantidade * preco) AS total FROM compras WHERE compra_id = $compra_id")->fetchColumn();
                ?>
                <tr>
                    <td data-label="Fornecedor"><?= htmlspecialchars($dados['fornecedor_razao'] ?? 'Fornecedor não cadastrado') ?></td>
                    <td data-label="NFE"><?= htmlspecialchars($num_nfe) ?></td>
                    <td data-label="Comprador"><?= htmlspecialchars($comprador) ?></td>
                    <td data-label="R$" class="text-center">R$: <?= number_format($soma, 2, ",", ".") ?></td>
                    <td data-label="Compra" class="text-center"><?= date('d/m/Y', strtotime($dados['cad_data'])) ?></td>
                    <td data-label="Entrada" class="text-center">
                        <?= $dados['ent_data'] ? date('d/m/Y', strtotime($dados['ent_data'])) : '--:--:--' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <br><div class="trigger alert">Nenhum registro encontrado!</div>
<?php endif; ?>

<?php if ($totalPaginas > 1): ?>
    <nav aria-label="Navegação de páginas">
        <ul class="pagination justify-content-center mt-3">

            <!-- Botão Anterior -->
            <?php if ($paginaAtual > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaAtual - 1])) ?>">Anterior</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Anterior</span></li>
            <?php endif; ?>

            <!-- Números de página (estilo compacto) -->
            <?php
                $inicio = max(1, $paginaAtual - 2);
                $fim = min($totalPaginas, $paginaAtual + 2);

                if ($inicio > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => 1])) . '">1</a></li>';
                    if ($inicio > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }

                for ($i = $inicio; $i <= $fim; $i++): ?>
                    <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($fim < $totalPaginas) {
                    if ($fim < $totalPaginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => $totalPaginas])) . '">' . $totalPaginas . '</a></li>';
                }
            ?>

            <!-- Botão Próximo -->
            <?php if ($paginaAtual < $totalPaginas): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaAtual + 1])) ?>">Próximo</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Próximo</span></li>
            <?php endif; ?>

        </ul>
    </nav>
<?php endif; ?>

