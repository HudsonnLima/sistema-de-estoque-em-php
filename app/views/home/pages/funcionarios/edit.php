<?php

ob_start();

// Ativar exibição de erros PHP
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

// Captura e filtra o parâmetro 'url' da URL
$userId = filter_input(INPUT_GET, 'userId', FILTER_DEFAULT);

$data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Verifica se a URL foi informada
if (!$userId) {
    $_SESSION['erro'] = "Usuário não especificado.";
    header("Location: " . BASE_URL . "/membros");
    exit;
}

// Consulta segura ao banco para buscar o user_id pela URL
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE user_id = :userId");
$stmt->execute(['userId' => 1]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o usuário foi encontrado
if (!$result) {
    $_SESSION['erro'] = "Usuário não encontrado.";
    header("Location: " . BASE_URL . "/membros");
    exit;
}

if ($result['user_id'] !== $_SESSION['user_id']) {
    $_SESSION['erro'] = "Você não tem permissão para editar outros usuários.";
    header("Location: " . BASE_URL . "/edit");
    exit;
}


$data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

$read = new Read();
$read->fetch("SELECT * FROM users WHERE user_id = :userId", ['userId' => $userId]);
$result = $read->getResult();
$count = $read->getRowCount();


if ($count < 1) {
    header("Location: " . BASE_URL . "/usuarios");
    $_SESSION['erro'] = "Erro: Não foi possível encontrar o funcionário com id informado.";
    die;
}


echo "<br/>";
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
echo "<br/>";
echo "<h4>Editar Perfil:</h4>";

echo "<hr>";

?>


<form id="cadastro" method="POST" enctype="multipart/form-data">
    <div class="row g-2">
        <label for="Dados Pessoais" class="form-label"><strong>Dados pessoais:</strong></label>

        <input type="hidden" class="form-control" id="user_id" name="user_id" placeholder="Nome" value="<?php echo isset($result['user_id']) ? $result['user_id'] : ''; ?>">

        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Nome" value="<?php echo isset($result['user_name']) ? $result['user_name'] : ''; ?>">
                <label for="Nome">Nome completo:</label>
            </div>
        </div>

                <input hidden type="text" class="form-control" id="user_function_id" name="user_function_id" placeholder="Função" value="<?php echo isset($result['user_function_id']) ? $result['user_function_id'] : ''; ?>">



        <div class="col-md-3">
            <div class="form-floating">
                <?php
                $selectedEnterprise = $formSource['empresa_id'] ?? ($result['empresa_id'] ?? '');
                ?>
                <select class="form-select" id="empresa_id" name="empresa_id" aria-label="">
                    <option disabled <?= empty($selectedEnterprise) ? 'selected' : ''; ?>>Selecione a empresa</option>
                    <?php
                    $read->fetchAll("SELECT * FROM empresas ORDER BY empresa_nome ASC");
                    $enterprise = $read->getResult();
                    foreach ($enterprise as $empresa):
                        $selected = ($selectedEnterprise == $empresa['empresa_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $empresa['empresa_id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($empresa['empresa_nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="floatingSelectGrid">Empresa:</label>
            </div>
        </div>

        <?php
        $setorSalvo = $result['user_setor_id'] ?? '';
        $grupoSalvo = $result['user_grupo_id'] ?? '';
        ?>

        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" id="grupo_id" name="user_grupo_id"
                    data-setor-salvo="<?= htmlspecialchars($result['user_setor_id'] ?? '') ?>">
                    <option disabled <?= empty($result['user_grupo_id']) ? 'selected' : ''; ?>>Selecione o grupo</option>
                    <?php
                    $read->fetchAll("SELECT * FROM setor_grupo ORDER BY setor_grupo_nome ASC");
                    $enterprise = $read->getResult();
                    foreach ($enterprise as $empresa):
                        $selected = ($result['user_grupo_id'] == $empresa['setor_grupo_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $empresa['setor_grupo_id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($empresa['setor_grupo_nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="grupo_id">Grupo:</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" id="setor_id" name="user_setor_id">
                    <option disabled selected value="0">Carregando...</option>
                </select>
                <label for="user_setor">Setor:</label>
            </div>
        </div>

        
        <div class="col-md-2">
            <div class="form-floating">
                <?php
                // Pega valor atual do status (do banco ou do form)
                $currentStatus = $result['user_status'] ?? '';
                ?>
                <select class="form-select" name="user_status" id="user_status">
                    <option disabled <?= $currentStatus === '' ? 'selected' : ''; ?> value=""> Status </option>
                    <option value="0" <?= $currentStatus == '0' ? 'selected' : ''; ?>>Ativo</option>
                    <option value="1" <?= $currentStatus == '1' ? 'selected' : ''; ?>>Inativo</option>
                </select>
                <label for="floatingSelectGrid">Status:</label>
            </div>
        </div>


        <input type="datetime-local" name="user_lastupdate" class="form-control" id="user_lastupdate" value="<?php echo date('Y-m-d\TH:i'); ?>" hidden>



        <p>
            <hr>
    </div>
    <button type="submit" id="editBtn" name="updateBtn" class="btn btn-primary mt-3">Atualizar usuário</button>
</form>


<script>
    const API_URL = "<?= API_URL ?>";
    const BASE_URL = "<?= BASE_URL ?>";
    const BASEJS = "<?= BASEJS ?>";
</script>


<!-- Scripts que manipulam validação e select -->
<script src="<?= BASEJS ?>/funcionarios/verificar_usuario.js"></script>
<script src="<?= BASEJS ?>/funcionarios/form_user.js"></script>
<script src="<?= BASEJS ?>/funcionarios/busca_setor.js"></script>

<?php ob_end_flush(); ?>