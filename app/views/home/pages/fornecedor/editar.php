<?php

use App\Core\Database;
use App\Models\Read;
use App\Controllers\ControllerFornecedor;

// instancia o PDO
$db = new Database();
$pdo = $db->getConnection();

// instancia Read (se sua classe Read precisa de $pdo, faça new Read($pdo))
$read = new Read();

// instancia o controller
$controller = new ControllerFornecedor($pdo);
$controller->handleRequest();

// agora pega o fornecedor para popular o form
$fornecedorId = filter_input(INPUT_GET, 'fornecedor_id', FILTER_VALIDATE_INT);
$query = "SELECT * FROM fornecedor WHERE fornecedor_id = :fornecedor_id";
$fornecedor = $read->fetch($query, ['fornecedor_id' => $fornecedorId]);

if (!empty($_SESSION['flash_message'])) {
    $msg = $_SESSION['flash_message'];
    $type = $msg['type'] === 'error' ? 'danger' : $msg['type'];
    echo "<div class='alert alert-{$type}'>{$msg['text']}</div>";
    unset($_SESSION['flash_message']);
}
?>

<label class="titulo">Editar fornecedor:</label>
<hr>

<form name="formulario" method="post" enctype="multipart/form-data">

    <div class="row g-2">

    <input type="hidden" name="fornecedor_id" id="fornecedor_id" value="<?= htmlspecialchars($fornecedor['fornecedor_id'] ?? '') ?>">

        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" maxlength="18" name="fornecedor_cnpj" id="fornecedor_cnpj" onkeypress="$(this).mask('00.000.000/0000-00')" value="<?= htmlspecialchars($fornecedor['fornecedor_cnpj'] ?? '') ?>">
                <label for="">CNPJ:</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_ie" onkeypress="$(this).mask('000000000000')" value="<?= htmlspecialchars($fornecedor['fornecedor_ie'] ?? '') ?>">
                <label for="">Inscrição Estadual:</label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" id="" class="form-control" name="fornecedor_razao" value="<?= htmlspecialchars($fornecedor['fornecedor_razao'] ?? '') ?>">
                <label for="floatingInputGrid">Razão Social:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_fantasia" id="" value="<?= htmlspecialchars($fornecedor['fornecedor_fantasia'] ?? '') ?>">
                <label for="">Nome Fantasia:</label>
            </div>
        </div>


        <div class="form-group col-md-6">
            <div class="form-floating ">
                <select class="form-select" name="fornecedor_categoria" id="" aria-label="">
                    <?php
                    $query = "SELECT * FROM fornecedor_cat ORDER BY fornecedor_categoria ASC";
                    $fornecedoresCat = $read->fetchAll($query);

                    if (empty($fornecedoresCat)) :
                        echo '<option disabled="disabled" value=""> Cadastre antes uma categoria! </option>';
                    else :
                        echo '<option disabled selected value=""> Selecione uma categoria! </option>';
                        foreach ($fornecedoresCat as $cat) :
                    ?>
                            <option value="<?= $cat['fornecedor_categoria'] ?>"
                                <?= (isset($fornecedor['fornecedor_categoria']) && $fornecedor['fornecedor_categoria'] === $cat['fornecedor_categoria']) ? 'selected' : '' ?>>
                                <?= $cat['fornecedor_categoria'] ?>
                            </option>
                    <?php
                        endforeach;
                    endif;


                    ?>


                </select>
                <label for="floatingSelectGrid">Categoria de produtos::</label>
            </div>
        </div>
        <div hidden class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_date" value="<?= date('d/m/Y H:i:s'); ?>">
                <label for="floatingInputGrid">Data:</label>
            </div>
        </div>

        <label class="titulo">Endereço</label>
        <hr>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_cep" id="cep" onkeypress="$(this).mask('00.000-000')" value="<?= htmlspecialchars($fornecedor['fornecedor_cep'] ?? '') ?>">
                <label for="floatingInputGrid">CEP:</label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_endereco" id="rua" value="<?= htmlspecialchars($fornecedor['fornecedor_endereco'] ?? '') ?>">
                <label for="">Endereço:</label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_bairro" id="bairro" value="<?= htmlspecialchars($fornecedor['fornecedor_bairro'] ?? '') ?>">
                <label for="floatingInputGrid">Bairro:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_cidade" id="cidade" value="<?= htmlspecialchars($fornecedor['fornecedor_cidade'] ?? '') ?>">
                <label for="floatingInputGrid">Cidade:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_estado" id="uf" value="<?= htmlspecialchars($fornecedor['fornecedor_estado'] ?? '') ?>">
                <label for="floatingInputGrid">Estado:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_pais" id="pais" value="<?= htmlspecialchars($fornecedor['fornecedor_pais'] ?? '') ?>">
                <label for="floatingInputGrid">Pais:</label>
            </div>
        </div>

        <label class="titulo">Contato</label>
        <hr>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_telefone" id="fornecedor_telefone" onkeypress="$(this).mask('(00) 0000-0000')" value="<?= htmlspecialchars($fornecedor['fornecedor_telefone'] ?? '') ?>">
                <label for="">Telefone:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_celular" id="fornecedor_celular" onkeypress="$(this).mask('(00) 0000-00000')" value="<?= htmlspecialchars($fornecedor['fornecedor_celular'] ?? '') ?>">
                <label for="floatingInputGrid">Celular:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_email" value="<?= htmlspecialchars($fornecedor['fornecedor_email'] ?? '') ?>">
                <label for="floatingInputGrid">Email:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_site" value="<?= htmlspecialchars($fornecedor['fornecedor_site'] ?? '') ?>">
                <label for="floatingInputGrid">Site:</label>
            </div>
        </div>



    </div><br />
    <input type="submit" id="editSubmitBtn" value="Salvar alterações" name="editSubmitBtn" class="btn btn-success" />
    </div><br />

</form>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>const API_URL = "<?= API_URL ?>";</script>
<script type="text/javascript" src="<?= BASEJS ?>/fornecedor/buscarCnpj.js"></script>
<script type="text/javascript" src="<?= BASEJS ?>/buscaCep.js"></script>