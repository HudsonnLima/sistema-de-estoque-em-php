<?php
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Core\Database;
use App\Models\Read;

$db = new Database();
$pdo = $db->getConnection();
$read = new Read();
?>

<?php
// Define os valores com prioridade: GET → POST → Banco
$operacaoSelecionada = $_GET['operacao'] ?? $_POST['operacao'] ?? ($result['operacao'] ?? '');
$produto       = $_GET['produto']       ?? $_POST['produto']       ?? ($dados['produto']       ?? '');
$funcionario   = $_GET['funcionario']   ?? $_POST['funcionario']   ?? ($dados['funcionario']   ?? '');
$fornecedor    = $_GET['fornecedor']    ?? $_POST['fornecedor']    ?? ($dados['fornecedor']    ?? '');
$data_inicio   = $_GET['data_inicio']   ?? $_POST['data_inicio']   ?? ($dados['data_inicio']   ?? '');
$data_final    = $_GET['data_final']    ?? $_POST['data_final']    ?? ($dados['data_final']    ?? '');
$agrupar       = $_GET['agrupar']       ?? $_POST['agrupar']       ?? '';
// Escolhe se o campo de pesquisa é funcionário ou fornecedor
$campo = !empty($funcionario) ? 'funcionario' : (!empty($fornecedor) ? 'fornecedor' : 'funcionario');

$user_grupo_id = $_GET['user_grupo_id'] ?? ($result['user_grupo_id'] ?? '');
$user_setor_id = $_GET['user_setor_id'] ?? ($result['user_setor_id'] ?? '');
?>

<form id="searchForm" method="GET" class="row g-2">
    <div class="form-group col-md-2">
        <div class="form-floating">
            <select class="form-select" name="operacao" id="operacao">
                <option value="0" <?= ($operacaoSelecionada === '0' || $operacaoSelecionada === 0) ? 'selected' : '' ?>>Saída</option>
                <option value="1" <?= ($operacaoSelecionada === '1' || $operacaoSelecionada === 1) ? 'selected' : '' ?>>Entrada</option>
            </select>
            <label for="floatingSelectGrid">Operação:</label>
        </div>
    </div>

    <div class="form-group col-md-3">
        <div class="form-floating">
            <input class="form-control" id="produto" name="produto"
                value="<?= htmlspecialchars($produto) ?>">
            <label for="produto">Pesquisar por produto:</label>
        </div>
    </div>

    <div class="form-group col-md-3">
        <div class="form-floating">
            <input class="form-control" id="pesquisaInput" name="<?= $campo ?>"
                value="<?= htmlspecialchars(${$campo}) ?>">
            <label id="pesquisaLabel" for="pesquisaInput">
                <?= $campo === 'funcionario' ? 'Pesquisar por funcionário:' : 'Pesquisar por fornecedor:' ?>
            </label>
        </div>
    </div>

    <div class="form-group col-md-2">
        <div class="form-floating">
            <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                value="<?= htmlspecialchars($data_inicio) ?>">
            <label for="data_inicio">Data Início:</label>
        </div>
    </div>

    <div class="form-group col-md-2">
        <div class="form-floating">
            <input type="date" class="form-control" id="data_final" name="data_final"
                value="<?= htmlspecialchars($data_final) ?>">
            <label for="data_final">Data Final:</label>
        </div>
    </div>


    <div class="col-12">
        <button type="button" id="limparCampos" class="btn btn-secondary">Limpar Campos</button>
        <input type="submit" name="busca" id="pesquisa" class="btn btn-primary" value="Pesquisar">
    </div>
</form>

<br />
<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $params = [];
    $sql = '';

    // PAGINAÇÃO
    $limite = 100;
    $paginaAtual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $offset = ($paginaAtual - 1) * $limite;
    $totalRegistros = 0;

    if (isset($_GET['operacao']) && $_GET['operacao'] == 0) {

        $sqlBase = "FROM est_saidas AS s
            JOIN funcionarios AS f ON f.user_id = s.funcionario_id
            JOIN produtos AS p ON p.produto_id = s.produto_id
            WHERE 1=1";

        // FILTRO: data
        if (!empty($data_inicio) && !empty($data_final)) {
            $params['data_inicio'] = $data_inicio . ' 00:00:00';
            $params['data_final']  = $data_final  . ' 23:59:59';
            $sqlBase .= " AND s.cad_data BETWEEN :data_inicio AND :data_final";
        } elseif (!empty($data_inicio)) {
            $params['data_inicio'] = $data_inicio . ' 00:00:00';
            $sqlBase .= " AND s.cad_data >= :data_inicio";
        } elseif (!empty($data_final)) {
            $params['data_final'] = $data_final . ' 23:59:59';
            $sqlBase .= " AND s.cad_data <= :data_final";
        }

        // FILTRO: funcionário
        if (!empty($funcionario)) {
            $sqlBase .= " AND f.user_name LIKE :funcionario";
            $params['funcionario'] = '%' . $funcionario . '%';
        }

        // FILTRO: produto
        if (!empty($produto)) {
            $sqlBase .= " AND p.produto LIKE :produto";
            $params['produto'] = '%' . $produto . '%';
        }

        // Contagem total
        $countSql = "SELECT COUNT(*) AS total " . $sqlBase;
        $read->fetch($countSql, $params);
        $totalRegistros = (int) ($read->getResult()['total'] ?? 0);

        // Query final com limite
        $sql = "SELECT s.*, p.produto AS produto_nome, f.user_name AS funcionario_nome " .
            $sqlBase . " ORDER BY s.id DESC LIMIT $limite OFFSET $offset";
    } elseif (isset($_GET['operacao']) && $_GET['operacao'] == 1) {

        $sqlBase = "FROM est_entradas AS e
            JOIN fornecedor AS f ON f.fornecedor_id = e.fornecedor_id
            JOIN produtos AS p ON p.produto_id = e.produto_id
            WHERE 1=1";

        // FILTRO: fornecedor
        if (!empty($fornecedor)) {
            $sqlBase .= " AND f.fornecedor_razao LIKE :fornecedor";
            $params['fornecedor'] = '%' . $fornecedor . '%';
        }

        // FILTRO: data
        if (!empty($data_inicio) && !empty($data_final)) {
            $params['data_inicio'] = $data_inicio . ' 00:00:00';
            $params['data_final']  = $data_final  . ' 23:59:59';
            $sqlBase .= " AND e.cad_data BETWEEN :data_inicio AND :data_final";
        } elseif (!empty($data_inicio)) {
            $params['data_inicio'] = $data_inicio . ' 00:00:00';
            $sqlBase .= " AND e.cad_data >= :data_inicio";
        } elseif (!empty($data_final)) {
            $params['data_final'] = $data_final . ' 23:59:59';
            $sqlBase .= " AND e.cad_data <= :data_final";
        }

        // FILTRO: produto
        if (!empty($produto)) {
            $sqlBase .= " AND p.produto LIKE :produto";
            $params['produto'] = '%' . $produto . '%';
        }

        // Contagem total
        $countSql = "SELECT COUNT(*) AS total " . $sqlBase;
        $read->fetch($countSql, $params);
        $totalRegistros = (int) ($read->getResult()['total'] ?? 0);

        // Query final com limite
        $sql = "SELECT e.*, p.produto AS produto_nome, f.fornecedor_razao AS fornecedor_razao " .
            $sqlBase . " ORDER BY e.id DESC LIMIT $limite OFFSET $offset";
    }

    if (!empty($sql)) {
        $read->fetchAll($sql, $params);
        $result = $read->getResult();
        $count  = $read->getRowCount();

        echo "<p>{$count} resultados exibidos nesta página de um total de {$totalRegistros} registros.</p><br/>";
    }
}
?>


<?php if (empty($result)): ?>
    <tr>
        <td colspan="5" class="text-center"></td>
    </tr>
    <div class="alert alert-info text-center" role="alert">Nenhum registro encontrado.</div>
<?php else: ?>

    <table id="search_table" class="table table-striped">
        <thead>
            <tr class="table-dark">
                <th class="text-start">Produto</th>
                <?php if (isset($_GET['operacao']) and $_GET['operacao'] == 0) { ?>
                    <th class="text-start">Funcionario</th>
                <?php } elseif (isset($_GET['operacao']) and $_GET['operacao'] == 1) { ?>
                    <th class="text-start">Fornecedor</th>
                <?php
                }
                ?>
                <th class="text-center">Quantidade</th>

                <th class="text-center">Data da compra</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td
                        data-label="Produto"
                        class="text-start abrir-modal-produto"
                        style="cursor:pointer;"
                        data-id="<?= $row['produto_id'] ?>"
                        data-nome="<?= htmlspecialchars($row['produto_nome']) ?>"
                        data-operacao="<?= $_GET['operacao'] ?? 0 ?>">
                        <?= htmlspecialchars($row['produto_nome']) ?>
                    </td>

                    <?php if (isset($_GET['operacao']) && $_GET['operacao'] == 0): ?>
                        <td data-label="Funcionário"><?= htmlspecialchars($row['funcionario_nome']) ?></td>
                    <?php elseif (isset($_GET['operacao']) && $_GET['operacao'] == 1): ?>
                        <td data-label="Fornecedor"><?= htmlspecialchars($row['fornecedor_razao']) ?></td>
                    <?php endif; ?>
                    <td data-label="Quantidade" class='text-center'><?= htmlspecialchars($row['quantidade']) ?></td>
                    <td data-label="Data da compra" class='text-center'><?= htmlspecialchars(date('d/m/Y', strtotime($row['cad_data']))); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<!-- PAGINAÇÃO -->
<?php if (!empty($totalRegistros) && $totalRegistros > $limite): ?>
    <?php
    $totalPaginas = ceil($totalRegistros / $limite);
    $inicio = max(1, $paginaAtual - 2);
    $fim = min($totalPaginas, $paginaAtual + 2);
    ?>
    <nav aria-label="Navegação de páginas">
        <ul class="pagination justify-content-center mt-3">

            <!-- Anterior -->
            <?php if ($paginaAtual > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaAtual - 1])) ?>">Anterior</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Anterior</span></li>
            <?php endif; ?>

            <!-- Primeira página + reticências -->
            <?php if ($inicio > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => 1])) ?>">1</a>
                </li>
                <?php if ($inicio > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Números -->
            <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- Última página + reticências -->
            <?php if ($fim < $totalPaginas): ?>
                <?php if ($fim < $totalPaginas - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $totalPaginas])) ?>"><?= $totalPaginas ?></a>
                </li>
            <?php endif; ?>

            <!-- Próximo -->
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

<div class="modal fade" id="modalProduto" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalProdutoTitle">Detalhes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

      <div class="table-responsive">
        <table class="table table-striped">
          <thead id="modalProdutoHead"></thead>
          <tbody id="modalProdutoBody"></tbody>
        </table>
        </div>

      </div>
    </div>
  </div>
</div>




<script>
    const API_URL = "<?= API_URL ?>";
    const BASE_URL = "<?= BASE_URL ?>";
    const BASEJS = "<?= BASEJS ?>";
</script>

<script src="<?= BASEJS ?>/relatorios/busca_setor.js"></script>
<script src="<?= BASEJS ?>/relatorios/search.js"></script>
<script src="<?= BASEJS ?>/relatorios/_modal_produto_operacao.js"></script>