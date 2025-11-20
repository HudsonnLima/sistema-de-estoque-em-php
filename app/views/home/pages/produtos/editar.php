<?php

use App\Core\Database;
use App\Models\Read;
use App\Controllers\ControllerProdutos;
use App\Models\ModelProdutos;

// =========================
// 1. Conexão com o banco
// =========================
$db = new Database();
$pdo = $db->getConnection();

// =========================
// 2. Controller → processa POST
// =========================
$controller = new ControllerProdutos($pdo);
$controller->handleRequest();

// =========================
// 3. Buscar produto para edição (GET)
// =========================
$produtoId = $_GET['produtoId'] ?? null;
$dados = [];
$modelProdutos = new ModelProdutos($pdo);
$dados = $modelProdutos->getProdutoById($produtoId);

// =========================
// 4. Recuperar formData (erros do POST)
// =========================
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Se não houver erro de POST → usa dados do banco para preencher o formulário
if ($produtoId && empty($formData)) {
    $formData = $dados;
}

// =========================
// 5. Flash message
// =========================
if (!empty($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];

    echo '
        <div class="alert alert-' . $flash['type'] . '">
            ' . htmlspecialchars($flash['text']) . '
        </div>
    ';

    unset($_SESSION['flash_message']);
}

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

<label class="titulo">Editar produto:</label>
<hr>

<form name="cadProdutos" method="post">
    <div class="row g-2">
        <input type="hidden" name="produto_id" value="<?= htmlspecialchars($formData['produto_id'] ?? '') ?>" required>

        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" name="produto" value="<?= htmlspecialchars($formData['produto'] ?? '') ?>" required>
                <label>Produto:</label>
            </div>
        </div>

        <div class="form-group col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" name="descricao" value="<?= htmlspecialchars($formData['descricao'] ?? '') ?>" required>
                <label>Descrição do Produto:</label>
            </div>
        </div>

        <div class="form-group col-md-2">
            <div class="form-floating">
                <input type="text" class="form-control" name="codigo" value="<?= htmlspecialchars($formData['codigo'] ?? '') ?>" required>
                <label>Código:</label>
            </div>
        </div>

        <div class="form-group col-md-2">
            <div class="form-floating">
                <select class="form-select" name="produto_status" required>
                    <option value="1" <?= (isset($formData['produto_status']) && $formData['produto_status'] == 1) ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= (isset($formData['produto_status']) && $formData['produto_status'] == 0) ? 'selected' : '' ?>>Desativado</option>
                </select>
                <label>Status:</label>
            </div>
        </div>

        <div class="form-group col-md-3">
            <div class="form-floating">
                <select class="form-select" name="medida_id" required>
                    <option value="" disabled>Selecione a Medida:</option>
                    <?php foreach ($medidas as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= (!empty($formData['medida_id']) && $formData['medida_id'] == $id) ? 'selected' : '' ?>>
                            <?= $nome ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Medida:</label>
            </div>
        </div>

        <div class="form-group col-md-3">
            <div class="form-floating">
                <select class="form-select" name="prioridade" required>
                    <option value="" disabled>Selecione a Prioridade</option>
                    <option value="1" <?= (!empty($formData['prioridade']) && $formData['prioridade'] == 1) ? 'selected' : '' ?>>Compra sem urgência</option>
                    <option value="2" <?= (!empty($formData['prioridade']) && $formData['prioridade'] == 2) ? 'selected' : '' ?>>Compra pouco Urgente</option>
                    <option value="3" <?= (!empty($formData['prioridade']) && $formData['prioridade'] == 3) ? 'selected' : '' ?>>Compra Urgente</option>
                </select>
                <label>Prioridade por compra:</label>
            </div>
        </div>

        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="estoque_minimo" name="alerta" value="<?= htmlspecialchars($formData['alerta'] ?? '') ?>" required>
                <label>Estoque Mínimo (Alerta):</label>
            </div>
        </div>

        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="estoque_maximo" name="estoque_maximo" value="<?= htmlspecialchars($formData['estoque_maximo'] ?? '') ?>" required>
                <label>Estoque Máximo:</label>
            </div>
        </div>

        <input hidden type="date" name="cad_data" class="form-control" value="<?= date('Y-m-d', strtotime($formData['cad_data'] ?? 'now')) ?>" readonly required>
        <input type="hidden" name="cad_autor" value="<?= htmlspecialchars($_SESSION['user_id']) ?>" required>
    </div><br />

    <input type="submit" value="Salvar alteração" id="submitBtn" name="submitBtn" class="btn btn-success">
</form>

<script src="<?= BASEJS ?>/estoque/validarCampos.js"></script>
