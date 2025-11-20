   <!-- Inclua o jQuery UI depois -->
   <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
   <!-- Inclua o CSS do jQuery UI, se necessário -->
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<?php
use App\Core\Database;
use App\Models\Read;
use App\Controllers\ControllerFuncionarios;
use App\Models\EmailFuncionarios;

$db = new Database();
$pdo = $db->getConnection();

$controller = new ControllerFuncionarios($pdo);
$controller->handleRequest(); 
$read = new Read();




    if (!empty($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        $type = $msg['type'] === 'error' ? 'danger' : $msg['type']; // Bootstrap alert class
        echo "<div class='alert alert-{$type}'>{$msg['text']}</div>";
        unset($_SESSION['flash_message']); // remove para nunca duplicar
    }
?>

   <script>
       document.addEventListener("DOMContentLoaded", function() {
           const alertBox = document.querySelector('.alert');
           if (alertBox) {
               setTimeout(() => {
                   alertBox.style.transition = "opacity 0.4s";
                   alertBox.style.opacity = "0";
                   setTimeout(() => alertBox.remove(), 400);
               }, 3500);
           }
       });
   </script>




<h4>Cadastrar funcionário:</h4>
<hr>

<form id="usuarios" method="POST">
    <div class="row g-2">
        <label class="form-label"><strong>Dados pessoais:</strong></label>

        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="user_name" name="user_name"
                    placeholder="Nome" value="<?= htmlspecialchars($formData['user_name'] ?? ''); ?>" required>
                <label for="user_name">Nome completo:</label>
            </div>
        </div>

        <input hidden type="text" class="form-control" id="user_function_id" name="user_function_id" placeholder="user_function_id" value="8" required>
       

        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="empresa_id" name="empresa_id" required>
                    <option disabled <?= empty($formData['empresa_id']) ? 'selected' : ''; ?>>Selecione a empresa</option>
                    <?php
                    $empresas = $read->fetchAll("SELECT * FROM empresas ORDER BY empresa_nome ASC");
                    foreach ($empresas as $empresa):
                        $selected = (isset($formData['empresa_id']) && $formData['empresa_id'] == $empresa['empresa_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $empresa['empresa_id'] ?>" <?= $selected ?> >
                            <?= htmlspecialchars($empresa['empresa_nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Empresa:</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" id="grupo_id" name="user_grupo_id" required>
                    <option disabled selected value="">Selecione grupo</option>
                    <?php
                    $grupos = $pdo->query("SELECT setor_grupo_id, setor_grupo_nome FROM setor_grupo ORDER BY setor_grupo_nome")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($grupos as $grupo):
                        $selected = (isset($formData['user_grupo_id']) && $formData['user_grupo_id'] == $grupo['setor_grupo_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $grupo['setor_grupo_id'] ?>" <?= $selected ?> >
                            <?= htmlspecialchars($grupo['setor_grupo_nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="grupo_id">Grupo:</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" id="setor_id" name="user_setor_id" disabled required>
                    <option selected disabled value="">Aguardando setor</option>
                </select>
                <label for="setor_id">Setor:</label>
            </div>
        </div>

        

        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" name="user_status" id="user_status" required>
                    <option disabled <?= isset($formData['user_status']) ? '' : 'selected'; ?>>Status</option>
                    <option value="0" <?= (!isset($formData['user_status']) || $formData['user_status'] == '0') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="1" <?= (isset($formData['user_status']) && $formData['user_status'] == '1') ? 'selected' : ''; ?>>Inativo</option>
                </select>

                <label>Status:</label>
            </div>
        </div>

    </div>

    <hr>
    <input type="submit" value="Cadastrar Funcionario" id="submitBtn" name="submitBtn" class="btn btn-success" />
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
                    <option value="<?= $empresa['empresa_id'] ?>" <?= $selected ?> >
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
            <label for="nome">Pesquisar por funcionários:</label>
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
<h4>Funcionários:</h4>

<!-- Modal de exclusão: agora fixo no HTML (moved from JS) -->
<div class="modal fade" id="modalExcluirUsuario" tabindex="-1" aria-labelledby="modalExcluirUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalExcluirUsuarioLabel">Confirmar exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div id="modalDetalheUsuario"></div>
        <hr>
        Tem certeza que deseja excluir o usuário?
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" id="confirmarExclusao">Excluir</button>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nome</th>
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
                $usuarios = $pdo->query("SELECT * FROM users WHERE user_function_id >= 8 ORDER BY user_name ASC, user_id ASC")->fetchAll(PDO::FETCH_ASSOC);
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
                        
                        <td><?= htmlspecialchars($empresa) ?></td>
                        <td><?= htmlspecialchars($setor) ?></td>
                        <td style="text-align:center">
                            <a href="<?= BASE_URL ?>/funcionarios/edit?userId=<?= $user['user_id'] ?>"><img src="<?= BASE_IMG ?>/edit.png" width="20" title="Editar"></a>
                            <a data-id="<?= $user['user_id']; ?>" class="excluirUsuario"><img src="<?= BASE_IMG ?>/del.png" width="20" title="Excluir"></a>
                        </td>
                    </tr>
            <?php
                endforeach;
            else:
                echo '<tr><td colspan="5">Nenhum funcionário encontrado.</td></tr>';
            endif;
            ?>
        </tbody>
    </table>
</div>

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

<script>
    const API_URL = "<?= API_URL ?>";
    const BASE_URL = "<?= BASE_URL ?>";
    const BASEJS = "<?= BASEJS ?>";
</script>

<!-- Scripts que manipulam validação e select -->
<script src="<?= BASEJS ?>/funcionarios/verificar_usuario.js"></script>
<script src="<?= BASEJS ?>/funcionarios/form_user.js"></script>
<script src="<?= BASEJS ?>/funcionarios/busca_setor.js"></script>
<script src="<?= BASEJS ?>/funcionarios/excluirFuncionarios.js"></script>


