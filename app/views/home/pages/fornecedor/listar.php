<?php

use App\Core\Database;
use App\Models\Read;

$read = new Read();

// Parâmetros de busca e paginação
$search_cnpj = isset($_GET['cnpj']) ? trim($_GET['cnpj']) : '';
$search_nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Montagem dinâmica do WHERE
$where = [];
$params = [];

if (!empty($search_cnpj)) {
    $cnpj_clean = preg_replace('/\D/', '', $search_cnpj);
    $where[] = "REPLACE(REPLACE(REPLACE(fornecedor_cnpj, '.', ''), '-', ''), '/', '') LIKE :cnpj";
    $params[':cnpj'] = "%$cnpj_clean%";
}

if (!empty($search_nome)) {
    $where[] = "(fornecedor_razao LIKE :nome OR fornecedor_fantasia LIKE :nome)";
    $params[':nome'] = "%$search_nome%";
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = "WHERE " . implode(' AND ', $where);
}

// Contar total de registros
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM fornecedor $whereSql");
$countStmt->execute($params);
$totalResults = $countStmt->fetchColumn();
$totalPages = ceil($totalResults / $limit);

// Buscar registros com limite e offset
$sql = "SELECT * FROM fornecedor $whereSql ORDER BY fornecedor_razao ASC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<label class="titulo">Fornecedores cadastrados:</label>
<hr>

<form id="search" method="GET" class="row g-2">

    <div class="form-group col-md-4">
        <div class="form-floating">
            <input type="text" name="cnpj" id="cnpj" class="form-control" maxlength="18" onkeypress="$(this).mask('00.000.000/0000-00')" value="<?= htmlspecialchars($search_cnpj) ?>">
            <label for="produto">Pesquisar por cnpj:</label>
        </div>
    </div>


    <div class="form-group col-md-6">
        <div class="form-floating">
            <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($search_nome) ?>">
            <label for="produto">Pesquisar por fornecedor:</label>
        </div>
    </div>

    <div class="form-group col-md-2 d-flex align-items-center gap-2">
        <button type="submit" class="btn-submit w-100">Buscar</button>
        <button type="button" id="limparFiltros" class="btn-clean w-100">Limpar</button>
    </div>
</form>
<br />
<script>
    document.getElementById('search').addEventListener('submit', function(e) {
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

<?php
if (empty($result)) {
    echo "<p>Nenhum fornecedor encontrado.</p>";
} else {
?>
    <table class='table table-striped align-middle'>
        <thead>
            <tr class='table-dark text-center'>
                <th>CNPJ</th>
                <th class='text-start'>Razão Social</th>
                <th class='text-start'>Nome Fantasia</th>
                <th class='text-start'>Categoria</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($result as $fornecedor) {
            ?>
                <tr>
                    <td class='text-center'><?= $fornecedor['fornecedor_cnpj'] ?></td>

                    <td class='text-start'>
                        <a href="#" class="nolink abrir-modal-fornecedor" data-id="<?= $fornecedor['fornecedor_id']; ?>"
                            data-nome="<?= htmlspecialchars($fornecedor['fornecedor_razao']); ?>"><?= $fornecedor['fornecedor_razao']; ?>
                        </a>
                    </td>

                    <td class='text-start'><?= $fornecedor['fornecedor_fantasia'] ?></td>
                    <td class='text-start'><?= $fornecedor['fornecedor_categoria'] ?></td>
                    <td class='text-center'>
                        <a href="<?= BASE_URL ?>/fornecedor/editar?fornecedor_id=<?= $fornecedor['fornecedor_id'] ?>"><img src="<?= BASE_IMG ?>/edit.png" width="20" title="Editar"></a>
                        <a data-id="<?= $fornecedor['fornecedor_id']; ?>" class="excluirProduto"><img src="<?= BASE_IMG ?>/del.png" width="20" title="Excluir"></a>


                    </td>
                </tr>
            <?php
            } ?>

        </tbody>
    </table>

    <?php

    // ===== PAGINAÇÃO =====
    $paginaAtual = $page;
    $totalPaginas = $totalPages;

    if ($totalPaginas > 1): ?>
        <nav aria-label="Navegação de páginas">
            <ul class="pagination justify-content-center mt-3">

                <!-- Anterior -->
                <?php if ($paginaAtual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaAtual - 1])) ?>">Anterior</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Anterior</span></li>
                <?php endif; ?>

                <!-- Números -->
                <?php
                $inicio = max(1, $paginaAtual - 2);
                $fim = min($totalPaginas, $paginaAtual + 2);

                if ($inicio > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a></li>';
                    if ($inicio > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }

                for ($i = $inicio; $i <= $fim; $i++):
                ?>
                    <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($fim < $totalPaginas) {
                    if ($fim < $totalPaginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => $totalPaginas])) . '">' . $totalPaginas . '</a></li>';
                }
                ?>

                <!-- Próximo -->
                <?php if ($paginaAtual < $totalPaginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaAtual + 1])) ?>">Próximo</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Próximo</span></li>
                <?php endif; ?>

            </ul>
        </nav>
<?php endif;
}
?>

<!-- Modal Bootstrap -->
<div class="modal fade" id="modalFornecedores" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Últimas compras de <span id="fornecedorNome"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th data-label="Fornecedor">Fornecedor</th>
                            <th data-label="Data da Compra">Data da Compra</th>
                            <th data-label="Data da Entrada">Data da Entrada</th>
                            <th data-label="Valor Total">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody id="modalFornecedoresBody">
                        <!-- Conteúdo via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    const API_URL = "<?= API_URL ?>";
</script>

<script type="text/javascript" src="<?= BASEJS ?>/fornecedor/buscarCnpj.js"></script>
<script type="text/javascript" src="<?= BASEJS ?>/buscaCep.js"></script>
<script src="<?= BASEJS ?>/modal_fornecedores.js"></script>