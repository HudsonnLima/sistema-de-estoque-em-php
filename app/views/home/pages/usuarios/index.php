<?php
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Core\Database;
use App\Controllers\ControllerUsuarios;
use App\Models\Read;

$db = new Database();
$pdo = $db->getConnection();
$read = new Read();

// Inicia Controller
$controller = new ControllerUsuarios($pdo);
$controller->handleRequest();

// Inicializa dados do formulário (edição ou pós-erro)
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // limpa após pegar

// Mensagens
$mensagem = '';
if (!empty($_SESSION['erro'])) {
    $mensagem = '<div class="alert alert-danger">' . $_SESSION['erro'] . '</div>';
    unset($_SESSION['erro']);
} elseif (!empty($_SESSION['sucesso'])) {
    $mensagem = '<div class="alert alert-success">' . $_SESSION['sucesso'] . '</div>';
    unset($_SESSION['sucesso']);
}

echo $mensagem;
?>



<h4>Cadastrar usuário:</h4>
<hr>

<form id="form" method="POST">
    <div class="row g-2">
        <label class="form-label"><strong>Dados pessoais:</strong></label>

        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="user_name" name="user_name"
                    placeholder="Nome" value="<?= htmlspecialchars($formData['user_name'] ?? ''); ?>" required>
                <label for="user_name">Nome completo:</label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="user_function_id" name="user_function_id" required>
                    <option disabled <?= empty($formData['user_function_id']) ? 'selected' : ''; ?>>Selecione função</option>
                    <?php
                    $functions = $read->fetchAll("SELECT * FROM user_function ORDER BY user_function_id ASC");
                    foreach ($functions as $funcao):
                        $selected = ($funcao['user_function_id'] == 8) ? 'selected' : '';
                    ?>
                        <option value="<?= $funcao['user_function_id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($funcao['user_function']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Função:</label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-floating">
                <input type="email" class="form-control" id="user_email" name="user_email"
                    placeholder="Email" value="<?= htmlspecialchars($formData['user_email'] ?? ''); ?>" required>
                <label for="user_email">Email:</label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="empresa_id" name="empresa_id" required>
                    <option disabled <?= empty($formData['empresa_id']) ? 'selected' : ''; ?>>Selecione a empresa</option>
                    <?php
                    $empresas = $read->fetchAll("SELECT * FROM empresas ORDER BY empresa_nome ASC");
                    foreach ($empresas as $empresa):
                        $selected = (isset($formData['empresa_id']) && $formData['empresa_id'] == $empresa['empresa_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $empresa['empresa_id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($empresa['empresa_nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Empresa:</label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="grupo_id" name="user_grupo_id" required>
                    <option disabled selected value="">Selecione grupo</option>
                    <?php
                    $grupos = $pdo->query("SELECT setor_grupo_id, setor_grupo_nome FROM setor_grupo ORDER BY setor_grupo_nome")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($grupos as $grupo):
                        $selected = (isset($formData['user_grupo_id']) && $formData['user_grupo_id'] == $grupo['setor_grupo_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $grupo['setor_grupo_id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($grupo['setor_grupo_nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="grupo_id">Grupo:</label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="setor_id" name="user_setor_id" disabled required>
                    <option selected disabled value="">Aguardando setor</option>
                </select>
                <label for="setor_id">Setor:</label>
            </div>
        </div>

        

        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" name="user_status" id="user_status" required>
                    <option disabled <?= isset($formData['user_status']) ? '' : 'selected'; ?>>Status</option>
                    <option value="0" <?= (!isset($formData['user_status']) || $formData['user_status'] == '0') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="1" <?= (isset($formData['user_status']) && $formData['user_status'] == '1') ? 'selected' : ''; ?>>Inativo</option>
                </select>

                <label>Status:</label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-floating">
                <input type="password" name="user_pass" class="form-control" id="user_pass" value="">
                <label for="user_pass">Senha:</label>
            </div>
        </div>

    </div>

    <hr>
    <input type="submit" value="Criar Usuário" id="submitBtn" name="submitBtn" class="btn btn-success" />
</form>

<br><br>

<form id="search" method="GET" class="row g-2">

    <!-- Select Empresa -->
    <div class="form-group col-md-4">
        <div class="form-floating">
            <select name="empresa_id" class="form-select">
                <option value="">Todas as empresas</option>
                <?php
                $empresas = $read->fetchAll("SELECT empresa_id, empresa_nome FROM empresas ORDER BY empresa_nome ASC");
                foreach ($empresas as $empresa):
                    $selected = (isset($_GET['empresa_id']) && $_GET['empresa_id'] == $empresa['empresa_id']) ? 'selected' : '';
                ?>
                    <option value="<?= $empresa['empresa_id'] ?>" <?= $selected ?>>
                        <?= htmlspecialchars($empresa['empresa_nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="empresa_id">Filtrar por empresa:</label>
        </div>
    </div>

    <!-- Input Nome -->
    <div class="form-group col-md-6">
        <div class="form-floating">
            <input type="text" name="nome" class="form-control"
                value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">
            <label for="nome">Pesquisar por usuário:</label>
        </div>
    </div>

    <!-- Botões -->
    <div class="form-group col-md-2 d-flex align-items-center gap-2">
        <button type="submit" class="btn-submit w-100">Buscar</button>
        <button type="button" id="limparFiltros" class="btn-clean w-100">Limpar</button>
    </div>
</form>

<script>
    document.getElementById('limparFiltros').addEventListener('click', function() {
        window.location.href = window.location.pathname;
    });
</script>
<br />

<!-- LISTAGEM DE USUÁRIOS -->
<h4>Usuários:</h4>
<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Empresa</th>
                <th>Setor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Recebe filtros da busca
            $filterEmpresa = $_GET['empresa_id'] ?? '';
            $filterNome = $_GET['nome'] ?? '';

            if ($filterEmpresa !== '' || $filterNome !== '') {
                // Se houver filtros, usa query preparada com WHERE
                $sql = "SELECT u.*, e.empresa_nome, s.setor_nome 
            FROM users u
            LEFT JOIN empresas e ON u.empresa_id = e.empresa_id
            LEFT JOIN setor s ON u.user_setor_id = s.setor_id
            WHERE 1=1";
                $params = [];

                if ($filterEmpresa !== '') {
                    $sql .= " AND u.empresa_id = :empresa_id";
                    $params['empresa_id'] = $filterEmpresa;
                }

                if ($filterNome !== '') {
                    $sql .= " AND u.user_name LIKE :nome";
                    $params['nome'] = '%' . $filterNome . '%';
                }

                $sql .= " ORDER BY u.user_name ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Sem filtros, usa query original
                $usuarios = $pdo->query("SELECT * FROM users WHERE user_function_id <= 7 ORDER BY user_name ASC, user_id ASC")->fetchAll(PDO::FETCH_ASSOC);
            }

            // Exibe os usuários
            if ($usuarios):
                foreach ($usuarios as $user):

                    // Empresa (apenas se não vier da query com JOIN)
                    if (!isset($user['empresa_nome'])) {
                        $empresaStmt = $pdo->prepare("SELECT empresa_nome FROM empresas WHERE empresa_id = :id");
                        $empresaStmt->execute(['id' => $user['empresa_id']]);
                        $empresa = $empresaStmt->fetch(PDO::FETCH_ASSOC)['empresa_nome'] ?? 'Não encontrada';
                    } else {
                        $empresa = $user['empresa_nome'];
                    }

                    // Setor (apenas se não vier da query com JOIN)
                    if (!isset($user['setor_nome'])) {
                        $setorStmt = $pdo->prepare("SELECT setor_nome FROM setor WHERE setor_id = :id");
                        $setorStmt->execute(['id' => $user['user_setor_id']]);
                        $setor = $setorStmt->fetch(PDO::FETCH_ASSOC)['setor_nome'] ?? 'Não encontrado';
                    } else {
                        $setor = $user['setor_nome'];
                    }
            ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_name']) ?? '' ?></td>
                        <td><?= htmlspecialchars($user['user_email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($empresa) ?></td>
                        <td><?= htmlspecialchars($setor) ?></td>
                        <td style="text-align:center">
                            <a href="<?= BASE_URL ?>/usuarios/edit?userId=<?= $user['user_id'] ?>"><img src="<?= BASE_IMG ?>/edit.png" width="20" title="Editar"></a>
                            <a data-id="<?= $user['user_id']; ?>" class="excluirUsuario"><img src="<?= BASE_IMG ?>/del.png" width="20"></a>
                        </td>
                    </tr>
            <?php
                endforeach;
            else:
                echo '<tr><td colspan="5">Nenhum usuário encontrado.</td></tr>';
            endif;
            ?>

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
        </tbody>
    </table>
</div>

<script>
    const API_URL = "<?= API_URL ?>";
    const BASE_URL = "<?= BASE_URL ?>";
    const BASEJS = "<?= BASEJS ?>";
</script>


<!-- Scripts que manipulam validação e select -->
<script src="<?= BASEJS ?>/usuarios/verificar_usuario.js"></script>
<script src="<?= BASEJS ?>/usuarios/form_user.js"></script>
<script src="<?= BASEJS ?>/usuarios/busca_setor.js"></script>
<script src="<?= BASEJS ?>/usuarios/excluirUsuario.js"></script>

<?php ob_end_flush(); ?>