<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">

<?php
use App\Core\Database;
use App\Controllers\ControllerCompras;
use App\Models\Read;

$db = new Database();
$pdo = $db->getConnection();

$controller = new ControllerCompras($pdo);
$controller->handleRequest();
 
$read = new Read();


$compraId = filter_input(INPUT_GET, 'compraId', FILTER_VALIDATE_INT);

$query = "SELECT * FROM compras WHERE compra_id = :compraId";
$compra = $read->fetch($query, ['compraId' => $compraId]);


if (!empty($_SESSION['success'])) {
    echo "<div class='alert alert-success'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}

if (!empty($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}
?>


<table class="table table-striped">
  <caption></caption>
  <thead>
    <tr class="table-dark">
      <td data-label="Fornecedor" style="width:45%" class="text-left">Fornecedor</td>
      <td data-label="Pagamento" style="width:10%" class="text-center">Pagamento</td>
      <td data-label="Comprado por" style="width:10%" class="text-center">Comprado por</td>
      <td data-label="Data da compra" style="width:10%" class="text-center">Data da compra</td>
      <td data-label="Prev de entrega" style="width:10%" class="text-center">Prev de entrega</td>
      <td data-label="Editar" style="width:5%" class="text-center">Editar</td>
    </tr>
  </thead>


  <tbody>
    <?php

    ?>
    <tr class="table-info">

      <td data-label="Fornecedor" class="text-left">
        <?php
        $fornecedor = $read->fetch("SELECT fornecedor_razao FROM fornecedor WHERE fornecedor_id = :fid", ['fid' => $compra['fornecedor_id']]);
        $fornecedorNome = $fornecedor['fornecedor_razao'] ?? "Fornecedor não encontrado.";
        echo $fornecedorNome;
        ?>
      </td>
      <td data-label="Pagamento" class="text-center"><?= $compra['pagamento_id']; ?></td>
      <td data-label="Comprado por" class="text-center">
        <?php
        $usuario = $read->fetch("SELECT user_name FROM users WHERE user_id = :uid", ['uid' => $compra['cad_autor']]);
        $userNome = $usuario['user_name'] ?? "Usuário não encontrado.";
        echo $userNome;
        ?>
      </td>
      <td data-label="Data da compra" class="text-center"><?= date('d/m/Y', strtotime($compra['cad_data'])); ?> </td>
      <?php
      //VERIFICA SE AS ENTREGAS ESTÃO DE ACORDO COM O PRAZO
      $hoje = date("d/m/Y");
      $previsao = date('d/m/Y', strtotime($compra['previsao']));
      $timeZone = new DateTimeZone('UTC');
      /** Assumido que $dataEntrada e $dataSaida estao em formato dia/mes/ano */
      $data1 = DateTime::createFromFormat('d/m/Y', $hoje, $timeZone);
      $data2 = DateTime::createFromFormat('d/m/Y', $previsao, $timeZone);

      /* Testa se sao validas 
        if (!($data1 instanceof DateTime)) {
            echo 'Data de compra invalida!!.'.'<br/>';
        }

        if (!($data2 instanceof DateTime)) {
            echo 'Data programada invalida!!'.'<br/>';
        }
         */
      ?>

      <?php if ($data1 < $data2) { ?>
        <td data-label="Prev de entrega" style="color:green;" class="text-center"><?= date('d/m/Y', strtotime($compra['previsao'])); ?> </td>
      <?php } elseif ($data1 > $data2) { ?>
        <td data-label="Prev de entrega" style="color:red;" class="text-center"><?= date('d/m/Y', strtotime($compra['previsao'])); ?> </td>
      <?php } elseif ($data1 == $data2) { ?>
        <td data-label="Prev de entrega" style="color:blue;" class="text-center"><?= date('d/m/Y', strtotime($compra['previsao'])); ?> </td>
      <?php } ?>

      <td data-label="Editar" class="text-center">
        <a href="#" class="btnEditarCompra" data-id="<?= $compra['compra_id']; ?>" title="Editar Dados"><i class="fa fa-pencil mr-1 fa-fw icon"></i></a>
      </td>
    </tr>

  </tbody>
</table>







<form method="POST" action="">
  <input type="hidden" name="compra_id" value="<?php echo $compra['compra_id']; ?>">
  <input type="hidden" name="fornecedor_id" value="<?php echo $compra['fornecedor_id']; ?>">
  <input type="hidden" name="pagamento_id" value="<?php echo $compra['pagamento_id']; ?>">
  <input type="hidden" name="cad_data" value="<?php echo $compra['cad_data']; ?>">
  <input type="hidden" name="previsao" value="<?php echo $compra['previsao']; ?>">
  <input type="hidden" name="compra_id" value="<?php echo $compraId; ?>">


  <div id="produtos">
    <div class="row g-2 produto" id="saida_1">
      <div class="col-md-4">
        <div class="form-floating">
          <input type="text" class="form-control produtos" name="produto[]" id="produto_1" value="<?php if (isset($compra['produto'][''])) echo $compra['produto']; ?>" required />
          <label for="produto_1">Produto:</label>
        </div>
      </div>

      <input type="hidden" class="form-control" name="produto_id[]" id="produto_id_1" value="" required />
      <input type="hidden" class="form-control" name="codigo[]" id="codigo_1" value="" required />
      <input type="hidden" class="form-control" name="medida_id[]" id="medida_id_1" value="" required />

      <div class="col-md-2">
        <div class="form-floating">
          <input type="text" class="form-control quantidade" name="quantidade[]" id="quantidade_1" value=""
            required />
          <label for="quantidade_1">Quantidade:</label>
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-floating">
          <input type="text" class="form-control preco" maxlength="9" name="preco[]" id="preco_un_1" value="" required />
          <label for="preco_un_1">Preço UN:</label>
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-floating">
          <input type="text" class="form-control preco total" maxlength="9" name="" id="preco_tt_1" value="" required />
          <label for="preco_tt_1">Preço Total:</label>
        </div>
      </div>

      <div class="col-md-1">
        <div class="form-floating">
          <input readonly type="text" class="form-control" name="estoque[]" id="estoque_1" value="" required />
          <label for="estoque_1">Estoque:</label>
        </div>
      </div>
      <input type="hidden" class="form-control" name="cad_autor" id="" value="<?= "{$_SESSION['user_name']}"; ?>" />



      <div class="col-md-1 d-flex align-items-center">
        <button type="button" id="adicionar" class="btn-add">+Produtos</button>
      </div>
    </div>

  </div>
  <br />


  <input type="hidden" class="form-control" name="cad_autor" value="<?= $_SESSION['user_id']; ?>" />


  <input type="submit" name="submitBtnAdd" id="submitBtnAdd" value="Adicionar" class="btn btn-success" />
</form>
<br />

<br />
<table class="table table-striped">
  <caption></caption>
  <thead>
    <tr class="table-dark">
      <td data-label="Produto" style="width:50%" class="text-left">Produto</td>
      <td data-label="Quantidade" style="width:10%" class="text-left">Quantidade</td>
      <td data-label="Preço Un" style="width:10%" class="text-left">Preço Un</td>
      <td data-label="Preco Total" style="width:10%" class="text-left">Preco Total</td>
      <td data-label="Ações" style="width:10%" class="text-left" colspan="2">Ações</td>
    </tr>
  </thead>


  <tbody>
    <?php


    $compras = "SELECT * FROM compras WHERE compra_id = $compraId ORDER BY id ASC";
    $readCompras = $pdo->prepare($compras);
    $readCompras->execute();
    foreach ($readCompras as $dados) {
    ?>
      <tr>

        <td data-label="Produto"><a href="#" data-id='<?php echo $dados['produto_id']; ?>' class="exibirproduto link"><?php if (isset($dados['produto'])) echo mb_convert_encoding($dados['produto'], 'Windows-1252', 'UTF-8'); ?></a></td>

        <td data-label="Quantidade"><?php if (isset($dados['quantidade'])) echo $dados['quantidade']; ?></td>
        <td data-label="Preço Un">R$: <?php if (isset($dados['preco'])) echo number_format($dados['preco'], 2, ",", ".") ?></td>

        <td data-label="Preco Total">R$:
          <?php
          $itens = $dados['quantidade'];
          $pc_un = $dados['preco'];
          $total = $itens * $pc_un;

          ?>

          <?= number_format($total, 2, ",", "."); ?>
        </td>

        <td data-label="Ações">
          <a href="#" data-id='<?php echo $dados['produto_id']?>' class="excluirproduto link" title="Excluir"><i class="fas fa-trash mr-1 icon" title="Excluir"></i></a>
        </td>

      </tr>

      <input type="hidden" class="form-control" name="id[]" value="<?php if (isset($dados['id'])) echo $dados['id']; ?>" />
      <input type="hidden" class="form-control" name="compra_id" value="<?php if (isset($dados['compra_id'])) echo $dados['compra_id']; ?>" />
      <input type="hidden" class="form-control" name="produto_id[]" value="<?php if (isset($dados['produto_id'])) echo $dados['produto_id']; ?>" />

    <?php  }; ?>
  </tbody>
</table>



<script>
  const API_URL = "<?= API_URL ?>";
</script>
<?php require __DIR__ . '/_modal_editar.php'; ?>
<?php require __DIR__ . '/_modal_excluir.php'; ?>

<script src="<?= BASEJS ?>/compras/compras.js"></script>
<script src="<?= BASEJS ?>/compras/excluirProduto.js"></script>
<script src="<?= BASEJS ?>/compras/autocomplete.js"></script>
<script src="<?= BASEJS ?>/compras/checkdate.js"></script>
<script src="<?= BASEJS ?>/compras/inputdinamico.js"></script>
<script src="<?= BASEJS ?>/compras/maskmoney.js"></script>
<script src="<?= BASEJS ?>/compras/excluirCompra.js"></script>