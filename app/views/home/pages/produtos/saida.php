   <!-- Inclua o jQuery UI depois -->
   <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
   <!-- Inclua o CSS do jQuery UI, se necessário -->
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">

<?php

use App\Core\Database;
$db = new Database();
use App\Controllers\ControllerEstoqueSaidas;

// Instancia Controller e dispara o POST
$controller = new ControllerEstoqueSaidas($pdo);
$controller->handleRequest();

$pdo = $db->getConnection();

    if (!empty($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        $type = $msg['type'] === 'error' ? 'danger' : $msg['type'];
        echo "<div class='alert alert-{$type}'>{$msg['text']}</div>";
        unset($_SESSION['flash_message']);
    }

?>

<label class="titulo">Saída de produtos:</label>
<hr>



<form name="PostForm" method="post">
    <div class=" row g-2">

        <div class="col-md-10">
            <div class="form-floating ">
                <select class="form-select" name="funcionario_id" required>
                    <option selected disabled value="">Selecione o funcionário:</option>
                    <?php
                    $stmtUsuarios = $pdo->prepare("SELECT user_id, user_name FROM funcionarios ORDER BY user_name ASC");
                    $stmtUsuarios->execute();
                    $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($usuarios as $usuario) {
                        echo '<option value="' . htmlspecialchars($usuario['user_id']) . '">' . htmlspecialchars($usuario['user_name']) . '</option>';
                    }
                    ?>
                </select>

                <label for="floatingSelectGrid">Funcionario:</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="date" name="cad_data" id="data" class="form-control" required value="<?php if (isset($dados['cad_data'])) : echo $dados['cad_data'];
                                                                                                    else : echo date('Y-m-d');
                                                                                                    endif; ?>" />
                <label for="floatingInputGrid">Data</label>
            </div>
        </div>
    </div>

    <div id="produtos">

        <div class="row g-2 produto" id="saida_1">
            <div class="col-md-5">
                <div class="form-floating">
                    <input type="text" class="form-control produtos" name="produto[]" id="produto_1" value="<?php if (isset($dados['produto'][''])) echo $dados['produto']; ?>" required />
                    <label for="produto_1">Produto:</label>
                </div>
            </div>
            <div class=" col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control quantidade" name="quantidade[]" id="quantidade_1" required value="<?php if (isset($dados['quantidade'][''])) echo $dados['quantidade']; ?>" />
                    <label for="quantidade">Quantidade:</label>
                </div>
            </div>
            <div class=" col-md-2">
                <div class="form-floating">
                    <input readonly type="text" class="form-control alerta" name="alerta[]" id="alerta_1" required value="<?php if (isset($dados['alerta'][''])) echo $dados['alerta']; ?>" />
                    <label for="quantidade">Alerta:</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input readonly type="text" class="form-control" name="estoque[]" id="estoque_1" value="" required />
                    <label for="estoque_1">Estoque:</label>
                </div>
            </div>

            <input type="hidden" class="form-control" required name="produto_id[]" id="produto_id_1" />
            <input type="hidden" class="form-control" required name="preco[]" id="preco_1" />
            <input type="hidden" class="form-control" required name="codigo[]" id="codigo_1" />
            <input type="hidden" class="form-control" required name="prioridade[]" id="prioridade_1" />
            <input type="hidden" class="form-control" required name="medida_id[]" id="medida_id_id" />


            <input type="hidden" class="form-control" name="cad_autor" required value="<?= "{$_SESSION['user_id']}"; ?>" />

            <div class="col-md-1 d-flex align-items-center">
                <button type="button" id="adicionar" class="btn-add">+Produtos</button>
            </div>

        </div>

    </div>

    <br />
    <input type="submit" id="submitBtn" name="submitBtn" value="Confirmar saída" class="btn btn-success">

</form>



   <script>
       const API_URL = "<?= API_URL ?>";
   </script>

   <script src="<?= BASEJS ?>/estoque/autocomplete.js"></script>
   <script src="<?= BASEJS ?>/estoque/checkdate.js"></script>
   <script src="<?= BASEJS ?>/estoque/inputdinamico.js"></script>