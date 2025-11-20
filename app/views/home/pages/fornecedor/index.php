<?php
use App\Controllers\ControllerFornecedor;
use App\Core\Database;  
use App\Models\Read;
$read = new Read();



if (!empty($_SESSION['flash_message'])) {
    $msg = $_SESSION['flash_message'];
    $type = $msg['type'] === 'error' ? 'danger' : $msg['type'];
    echo "<div class='alert alert-{$type}'>{$msg['text']}</div>";
    unset($_SESSION['flash_message']);
}

$oldData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<label class="titulo">Cadastrar novo fornecedor:</label>
<hr>

<form name="fornecedorForm" method="post" enctype="multipart/form-data">

    <div class="row g-2">

        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" maxlength="18" name="fornecedor_cnpj" id="fornecedor_cnpj" onkeypress="$(this).mask('00.000.000/0000-00')" value="<?= htmlspecialchars($oldData['fornecedor_cnpj'] ?? '') ?>" required>
                <label for="">CNPJ:</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_ie" onkeypress="$(this).mask('000000000000')" value="<?= htmlspecialchars($oldData['fornecedor_ie'] ?? '') ?>" required>
                <label for="">Inscrição Estadual:</label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" id="" class="form-control" name="fornecedor_razao" value="<?= htmlspecialchars($oldData['fornecedor_razao'] ?? '') ?>" required>
                <label for="floatingInputGrid">Razão Social:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_fantasia" id="" value="<?= htmlspecialchars($oldData['fornecedor_fantasia'] ?? '') ?>" required>
                <label for="">Nome Fantasia:</label>
            </div>
        </div>


        <div class="form-group col-md-6">
            <div class="form-floating ">
                <select class="form-select" name="fornecedor_categoria" id="" aria-label="" required>
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
                                <?= (isset($oldData['fornecedor_categoria']) && $oldData['fornecedor_categoria'] === $cat['fornecedor_categoria']) ? 'selected' : '' ?> >
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
                <input type="text" class="form-control" name="fornecedor_date" value="<?= date('d/m/Y H:i:s'); ?>" required>
                <label for="floatingInputGrid">Data:</label>
            </div>
        </div>

        <label class="titulo">Endereço</label>
        <hr>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_cep" id="cep" onkeypress="$(this).mask('00.000-000')" value="<?= htmlspecialchars($oldData['fornecedor_cep'] ?? '') ?>" required>
                <label for="floatingInputGrid">CEP:</label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_endereco" id="rua" value="<?= htmlspecialchars($oldData['fornecedor_endereco'] ?? '') ?>" required>
                <label for="">Endereço:</label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_bairro" id="bairro" value="<?= htmlspecialchars($oldData['fornecedor_bairro'] ?? '') ?>" required>
                <label for="floatingInputGrid">Bairro:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_cidade" id="cidade" value="<?= htmlspecialchars($oldData['fornecedor_cidade'] ?? '') ?>" required>
                <label for="floatingInputGrid">Cidade:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_estado" id="uf" value="<?= htmlspecialchars($oldData['fornecedor_estado'] ?? '') ?>" required>
                <label for="floatingInputGrid">Estado:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_pais" id="pais" value="<?= htmlspecialchars($oldData['fornecedor_pais'] ?? '') ?>" required>
                <label for="floatingInputGrid">Pais:</label>
            </div>
        </div>

        <label class="titulo">Contato</label>
        <hr>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_telefone" id="fornecedor_telefone" onkeypress="$(this).mask('(00) 0000-0000')" value="<?= htmlspecialchars($oldData['fornecedor_telefone'] ?? '') ?>" required>
                <label for="">Telefone:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_celular" id="fornecedor_celular" onkeypress="$(this).mask('(00) 0000-00000')" value="<?= htmlspecialchars($oldData['fornecedor_celular'] ?? '') ?>" required>
                <label for="floatingInputGrid">Celular:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_email" value="<?= htmlspecialchars($oldData['fornecedor_email'] ?? '') ?>" required>
                <label for="floatingInputGrid">Email:</label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" name="fornecedor_site" value="<?= htmlspecialchars($oldData['fornecedor_site'] ?? '') ?>" required>
                <label for="floatingInputGrid">Site:</label>
            </div>
        </div>



    </div><br />
    <input type="submit" id="submitBtn" value="Cadastrar Fornecedor" name="submitBtn" class="btn btn-success" />
    </div><br />

</form>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>const API_URL = "<?= API_URL ?>";</script>
<script type="text/javascript" src="<?= BASEJS ?>/fornecedor/buscarCnpj.js"></script>
<script type="text/javascript" src="<?= BASEJS ?>/buscaCep.js"></script>
<!--
<script type="text/javascript" src="<?= BASEJS ?>/fornecedor/validarCampos.js"></script>-->