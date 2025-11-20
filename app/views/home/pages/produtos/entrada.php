<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Core\Database;
use App\Models\Read;
use App\Controllers\ControllerEstoqueEntradas;

$db = new Database();
$pdo = $db->getConnection();

// Instancia Controller e dispara o POST
$controller = new ControllerEstoqueEntradas($pdo);
$controller->handleRequest();

$read = new Read();
$compraId = filter_input(INPUT_GET, 'compraId', FILTER_VALIDATE_INT);

// Busca a compra
$compra = $read->fetchAll("SELECT * FROM compras WHERE compra_id = :id", ['id' => $compraId]);
$compraCabecalho = $compra[0] ?? [];

// Validação do estado
if (!empty($compraCabecalho) && $compraCabecalho['estado'] != 0) {
    header('Location: ' . BASE_URL. '/estoque/entradas');
    exit;
}

?>

<label class="titulo">Entrada de produtos:</label>
<hr>

<form method="POST">

    <div class="row g-2">

        <input type="hidden" class="form-control" name="compra_id" value="<?php if (isset($compraCabecalho['compra_id'])) echo $compraCabecalho['compra_id']; ?>" />

        <div class="col-md-6">
            <div class="form-floating x">
                <input readonly type="text" name="" class="form-control" id="" value="<?php
                                                                                        $stmtFornecedor = "SELECT * FROM fornecedor WHERE fornecedor_id = :fornecedor_id";
                                                                                        $fornecedor = $read->fetch($stmtFornecedor, ['fornecedor_id' => $compraCabecalho['fornecedor_id']]);
                                                                                        echo $fornecedor['fornecedor_razao'] ?? 'Fornecedor não encontrado';
                                                                                        ?>" />
                <label for="floatingInputGrid">Fornecedor:</label>
            </div>
        </div>


        <div class="col-md-2">
            <div class="form-floating ">
                <input readonly type="text" name="pagamento_id" class="form-control" id="pagamento_id" value="<?= $compraCabecalho['pagamento_id'] ?>" />
                <label for="floatingInputGrid">Pagamento:</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input readonly type="date" name="cad_data" class="form-control" id="" value="<?php if (isset($compraCabecalho['cad_data'])) : echo $compraCabecalho['cad_data'];
                                                                                                else : echo date('Y-m-d');
                                                                                                endif; ?>" />
                <label for="floatingInputGrid">Data da compra:</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="date" name="ent_data" class="form-control" id="data" value="<?php echo date('Y-m-d'); ?>" />
                <label for="floatingInputGrid">Data de entrada:</label>
            </div>
        </div>


        <div class="col-md-8">
            <div class="form-floating">
                <input type="text" class="form-control" name="chave_nfe" required value="<?php if (isset($compra['chave_nfe'])) echo $compra['chave_nfe']; ?>" />
                <label for="produto">Chave Nfe</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" name="num_nfe" required value="<?php if (isset($compra['num_nfe'])) echo $compra['num_nfe']; ?>" />
                <label for="produto">Num Nfe:</label>
            </div>
        </div>

        <input type="hidden" class="form-control" name="operacao" value="1" />

    </div>


    <br />

    <div class="row g-2">


        <?php foreach ($compra as $compra): ?>

            
            <div class="col-md-8">
                <div class="form-floating">
                    <input readonly type="text" class="form-control" name="" required value="<?= htmlspecialchars($compra['produto'] ?? ' ') ?>" />
                    <label for="produto">Produto</label>
                </div>
            </div>


            <input type="hidden" class="form-control" name="medida_id[]" id="medida_id" value="<?php if (isset($compra['medida_id'])) : echo $compra['medida_id'];
                                                                                                endif; ?>">
            <input require type="hidden" class="form-control" name="estado" value="1">
            <input require type="hidden" class="form-control" name="produto_id[]" value="<?php if (isset($compra['produto_id'])) echo $compra['produto_id']; ?>" />
            <input require type="hidden" class="form-control" name="codigo[]" required value="<?= $compra['codigo']?>">

            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control preco" name="preco[]" maxlength="9" required value="<?= $compra['preco'] ?? ' ' ?>" />
                    <label for="floatingInputGrid">Preço</label>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" name="quantidade[]" value="<?php if (isset($compra['quantidade'])) echo $compra['quantidade']; ?>" />
                    <label for="floatingInputGrid">Entrada</label>
                </div>
            </div>

             <?php
            $query = "SELECT * FROM produtos WHERE produto_id = :produto_id";
            $produto = $read->fetch($query, ['produto_id' => $compra['produto_id']]);

            $estoque_atual = $produto['estoque'] ?? 0;
            $novo_estoque = $estoque_atual + $compra['quantidade']; 

            ?>

            <div class="form-group col-md-2">
                <div class="form-floating">
                    <input readonly type="text" class="form-control" name="" value="<?= $estoque_atual ?>">
                    <label for="estoque">Estoque:</label>
                </div>
            </div>
            <input readonly type="hidden" class="form-control" name="estoque[]" value="<?= $novo_estoque ?>">

            <?php
            // Buscar último registro fiscal do produto, se existir
            $queryFiscal = "SELECT ncm, cst, cfop, icms, ipi FROM est_entradas WHERE produto_id = :produto_id ORDER BY id DESC LIMIT 1";
            $dadosFiscais = $read->fetch($queryFiscal, ['produto_id' => $compra['produto_id']]);
            ?>


            <div class="form-group col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" name="ncm[]" maxlength="8" required value="<?= $dadosFiscais['ncm'] ?? '' ?>">
                    <label for="ncm">NCM:</label>
                </div>
            </div>

            <div class="form-group col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" name="cst[]" maxlength="3" required value="<?= $dadosFiscais['cst'] ?? '' ?>">
                    <label for="cst">CST:</label>
                </div>
            </div>

            <div class="form-group col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" name="cfop[]" maxlength="4" required value="<?= $dadosFiscais['cfop'] ?? '' ?>">
                    <label for="cfop">CFOP:</label>
                </div>
            </div>

            <div class="form-group col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" name="icms[]" maxlength="2" required value="<?= $dadosFiscais['icms'] ?? '' ?>">
                    <label for="icms">ICMS:</label>
                </div>
            </div>

            <div class="form-group col-md-2">
                <div class="form-floating">
                    <input require type="text" class="form-control" name="ipi[]" maxlength="2" required value="<?= $dadosFiscais['ipi'] ?? '' ?>">
                    <label for="ipi">IPI:</label>
                </div>
            </div>
            <input require type="hidden" class="form-control" name="cad_autor" required value="<?= $_SESSION['user_id'] ?? '' ?>">

        <?php endforeach; ?>
    </div>
<br/>
    <input type="submit" value="Adicionar ao estoque" id="submitBtn" name="submitBtn" class="btn btn-success" />


</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script src="<?= BASEJS ?>/estoque/entrada.js"></script>