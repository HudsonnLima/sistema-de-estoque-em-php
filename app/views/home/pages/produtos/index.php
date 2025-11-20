<?php

use App\Core\Database;
use App\Models\Read;

$db = new Database();
$pdo = $db->getConnection();

use App\Controllers\ControllerProdutos;

// Instancia Controller e dispara o POST
$controller = new ControllerProdutos($pdo);
$controller->handleRequest();

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

if (!empty($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];

    echo '<div class="alert alert-' . $flash['type'] . '">
            ' . htmlspecialchars($flash['text']) . '
          </div>';

    unset($_SESSION['flash_message']);
}
?>
<label class="titulo">Cadastrar produto:</label>
<hr>



<form name="cadProdutos" method="post">
    <div class=" row g-2">
        <input type="hidden" name="produto_id" value="<?= $formData['produto_id'] ?? '' ?>">
        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" name="produto" value="<?= htmlspecialchars($formData['produto'] ?? ''); ?>" required>
                <label>Produto:</label>
            </div>
        </div>

        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" name="descricao" value="<?= htmlspecialchars($formData['descricao'] ?? ''); ?>" required>
                <label for="floatingInputGrid">Descrição do Produto:</label>
            </div>
        </div>

        <div class="form-group col-md-2">
            <div class="form-floating">
                <input type="text" class="form-control" name="codigo" value="<?= htmlspecialchars($formData['codigo'] ?? ''); ?>" required>
                <label for="floatingInputGrid">Código:</label>
            </div>
        </div>


        <div class="form-group col-md-2">
            <div class="form-floating ">
                <select class="form-select" name="produto_status" required>
                    <option selected value="1" <?php if (isset($dados['produto_status']) && $dados['produto_status'] == 1) echo 'selected="selected"'; ?>> Ativo </option>
                    <option value="0" <?php if (isset($dados['produto_status']) && $dados['produto_status'] == 0) echo 'selected="selected"'; ?>> Desativado </option>
                </select>
                <label for="floatingSelectGrid">Status:</label>
            </div>
        </div>


        <?php
        $medidas = [
            1 => 'Kilo',
            2 => 'Litro',
            3 => 'Metro',
            4 => 'Unidade',
            5 => 'Caixa',
            6 => 'Lata',
            7 => 'Pacote',
            8 => 'Bobina',
            9 => 'Par'
        ];
        ?>

        <div class="form-group col-md-3">
            <div class="form-floating ">
                <select class="form-select" name="medida_id" required>
                    <option value="" disabled selected>Selecione a Medida:</option>

                    <?php foreach ($medidas as $id => $nome): ?>
                        <?php
                        $selected = '';

                        if (!empty($formData['medida_id']) && $formData['medida_id'] == $id) {
                            $selected = 'selected';
                        } elseif (!empty($dados['medida_id']) && $dados['medida_id'] == $id) {
                            $selected = 'selected';
                        }
                        ?>
                        <option value="<?= $id ?>" <?= $selected ?>>
                            <?= $nome ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="floatingSelectGrid">Medida:</label>
            </div>
        </div>


        <div class="form-group col-md-3">
            <div class="form-floating ">
                <select class="form-select" name="prioridade" required>
                    <option value="" disabled selected>Selecione a Prioridade</option>

                    <option value="1" <?= (!empty($formData['prioridade']) && $formData['prioridade'] == 1) ? 'selected' : '' ?>>
                        Compra sem urgência
                    </option>

                    <option value="2" <?= (!empty($formData['prioridade']) && $formData['prioridade'] == 2) ? 'selected' : '' ?>>
                        Compra pouco Urgente
                    </option>

                    <option value="3" <?= (!empty($formData['prioridade']) && $formData['prioridade'] == 3) ? 'selected' : '' ?>>
                        Compra Urgente
                    </option>
                </select>

                <label for="floatingSelectGrid">Prioridade por compra:</label>
            </div>
        </div>



        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="estoque_minimo" name="alerta" value="<?= htmlspecialchars($formData['alerta'] ?? ''); ?>" required>
                <label for="floatingInputGrid">Estoque Minimo(Alerta):</label>
            </div>
        </div>

        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="estoque_maximo" name="estoque_maximo" value="<?= htmlspecialchars($formData['estoque_maximo'] ?? ''); ?>" required>
                <label for="floatingInputGrid">Estoque Máximo:</label>
            </div>
        </div>

        <input hidden  type="date" name="cad_data" class="form-control" value="<?php if (isset($formData['cad_data'])) : echo $formData['cad_data'];
                                                                                        else : echo date('Y-m-d');
                                                                                        endif; ?>" />

        <input type="hidden" readonly="readonly" class="form-control" name="cad_autor" value="<?= "{$_SESSION['user_id']}"; ?>" required>

    </div><br />
    <input type="submit" value="Cadastrar Produto" id="submitBtn" name="submitBtn" class="btn btn-success" />
</form>








<label class="titulo">Produtos:</label>
<hr>


<form id="search" method="GET" class="row g-2">
    
    <div class="col-md-4">
        <div class="form-floating">
            <select id="setor" name="setor_id" class="form-select">
                <option value="">Selecione o setor</option>
                <?php
                // Consulta setores
                $setorQuery = "SELECT setor_id, setor_nome FROM setor";
                $stmtSetor = $pdo->query($setorQuery);
                $setores = $stmtSetor->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($setores as $setor): ?>
                    <option value="<?= $setor['setor_id']; ?>"
                        <?= (isset($_GET['setor_id']) && $_GET['setor_id'] == $setor['setor_id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($setor['setor_nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="floatingSelectGrid">Setor:</label>
        </div>
    </div>

    <div class="form-group col-md-6">
        <div class="form-floating">
            <input class="form-control" id="produto" name="produto"
                value="<?= isset($_GET['produto']) ? htmlspecialchars($_GET['produto']) : ''; ?>">
            <label for="produto">Pesquisar por produto:</label>
        </div>
    </div>

    <div class="form-group col-md-2 d-flex align-items-center gap-2">
        <button type="submit" class="btn-submit w-100">Buscar</button>
        <button type="button" id="limparFiltros" class="btn-clean w-100">Limpar</button>
    </div>
</form>

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
require_once MODELS . '/Read.php';
$read = new Read();

$limite = 50;

// Página atual
$paginaAtual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $limite;

// Filtros
$setor_id = trim($_GET['setor_id'] ?? '');
$produto  = trim($_GET['produto'] ?? '');
$params   = [];
$where    = "WHERE 1=1";

if ($setor_id !== '') {
    $where .= " AND p.setor_id = ?";
    $params[] = $setor_id;
}
if ($produto !== '') {
    $where .= " AND p.produto LIKE ?";
    $params[] = "%{$produto}%";
}

// Conta total de registros
$countQuery = "SELECT COUNT(*) AS total 
               FROM produtos p
               LEFT JOIN setor s ON p.setor_id = s.setor_id
               $where";
$totalResultados = $read->fetch($countQuery, $params)['total'] ?? 0;
$totalPaginas = ceil($totalResultados / $limite);

// Consulta principal com limite
$query = "SELECT p.*, s.setor_nome
          FROM produtos p
          LEFT JOIN setor s ON p.setor_id = s.setor_id
          $where
          ORDER BY p.produto ASC
          LIMIT $limite OFFSET $offset";

$itens = $read->fetchAll($query, $params);
?>

<?php if (count($itens) > 0): ?>
    <div class="table-responsive mt-3">
        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Estoque Mínimo</th>
                    <th>Estoque Máximo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td data-label="Produto"><a class="nolink" href="<?= BASE_URL ?>/estoque/produtos?produtoId=<?= $item['produto_id'] ?>"><?= htmlspecialchars($item['produto']); ?></a></td>
                        <td data-label="Quantidade"><?= htmlspecialchars($item['estoque']); ?></td>
                        <td data-label="Estoque Mínimo"><?= htmlspecialchars($item['alerta']); ?></td>
                        <td data-label="Estoque Máximo"><?= htmlspecialchars($item['estoque_maximo']); ?></td>
                        <td data-label="Ações">
                            <a href="<?= BASE_URL ?>/estoque/produtos/editar?produtoId=<?= $item['produto_id']; ?>"><img src="<?= BASE_IMG ?>/edit.png" width="20" title="Editar"></a>
                            <a data-id="<?= $item['produto_id']; ?>" class="excluirProduto"><img src="<?= BASE_IMG ?>/del.png" width="20"></a>
                        </td>


                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINAÇÃO -->
    <?php if ($totalPaginas > 1): ?>
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

                <!-- Números -->
                <?php
                $inicio = max(1, $paginaAtual - 2);
                $fim = min($totalPaginas, $paginaAtual + 2);

                if ($inicio > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => 1])) . '">1</a></li>';
                    if ($inicio > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }

                for ($i = $inicio; $i <= $fim; $i++):
                ?>
                    <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($fim < $totalPaginas) {
                    if ($fim < $totalPaginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => $totalPaginas])) . '">' . $totalPaginas . '</a></li>';
                }
                ?>

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

<?php else: ?>
    <p class="mt-3">Nenhum produto encontrado.</p>
<?php endif; ?>

<script src="<?= BASEJS ?>/estoque/validarCampos.js"></script>