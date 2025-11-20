<?php

use App\Core\Database;
use App\Models\Read;

$db = new Database();
$pdo = $db->getConnection();
?>


<?php
if (!empty($_SESSION['flash_message'])) {
    $msg = $_SESSION['flash_message'];
    $type = $msg['type'] === 'error' ? 'danger' : $msg['type']; // Bootstrap alert class
    echo "<div class='alert alert-{$type}'>{$msg['text']}</div>";
    unset($_SESSION['flash_message']); // remove para nunca duplicar
}
?>
<label class="titulo">Entradas de produtos:</label>
<hr>

<?php

$query = "SELECT compra_id, fornecedor_id, cad_data, previsao,cad_autor, SUM(preco * quantidade) AS total_compra 
FROM compras WHERE estado = 0 GROUP BY compra_id, fornecedor_id, cad_data, previsao, cad_autor ORDER BY cad_data DESC";
$itens = $read->fetchAll($query);

/*
SELECT fornecedor_id, cad_data, previsao, SUM(preco * quantidade) FROM `compras` WHERE estado = 0
*/

if (!$itens) {
    echo "<p>Nenhuma compra encontrada.</p>";
    return;
}

?>


<div class="table-responsive mt-3">
    <table class="table table-striped">
        <thead>
            <tr class="table-dark">
                <th class="text-start" style="width: 40%;">Fornecedor</th>
                <th>Total</th>
                <th>Data Compra</th>
                <th>Previsão entrega</th>
                <th>Itens</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>

            <?php

            foreach ($itens as $item):
                $compra_id = $item['compra_id'];
                $cad_data  = date('d/m/Y', strtotime($item['cad_data']));
                $previsao  = date('d/m/Y', strtotime($item['previsao']));
                $previsao_ts = strtotime($item['previsao']); // timestamp da previsão
                $hoje_ts    = strtotime(date('Y-m-d'));      // timestamp de hoje
                $total      = number_format($item['total_compra'], 2, ',', '.');


            ?>

                <tr>
                    <?php
                    $fornecedor_id = $item['fornecedor_id'];

                    $query = "SELECT fornecedor_razao 
          FROM fornecedor 
          WHERE fornecedor_id = :fornecedor_id";

                    $fornecedor = $read->fetchAll($query, ['fornecedor_id' => $fornecedor_id]);

                    // Pega o nome do fornecedor se existir
                    $fornecedor_nome = $fornecedor[0]['fornecedor_razao'] ?? '';
                    ?>
                    <td data-label="Fornecedor">
                        <a href="#" class="nolink abrir-modal-fornecedor" data-id="<?= $fornecedor_id; ?>"
                            data-nome="<?= htmlspecialchars($fornecedor_nome); ?>"><?= htmlspecialchars($fornecedor_nome); ?>
                        </a>
                    </td>

                    <td data-label="R$">R$: <?= $total ?></td>
                    <td data-label="Data Compra"><?= $cad_data ?></td>
                    <td data-label="Previsão entrega">
                        <?php
                        if ($previsao_ts > $hoje_ts) {
                            echo "<span class='prazo'>$previsao</span><br />";
                        } elseif ($previsao_ts == $hoje_ts) {
                            echo "<span class='hoje'>$previsao</span><br />";
                        } else {
                            echo "<span class='atrasado'>$previsao</span><br />";
                        } ?>
                    </td>
                    <?php
                    $comp_id = $item['compra_id'];
                    $quantidade = $pdo->query("SELECT SUM(quantidade) AS quant FROM compras WHERE compra_id = $comp_id")->fetchColumn();

                    $qnt = "SELECT SUM(quantidade) FROM compras WHERE estado = 0 AND compra_id = $comp_id";
                    $data = $pdo->prepare($qnt);
                    $data->execute();
                    $result = $data->fetchColumn();


                    ?>
                    <td data-label="Itens"><?php echo $quantidade; ?></td>

                    <td data-label="Ações" class="text-center">
                        <a href="<?= BASE_URL; ?>/produtos/entrada?compraId=<?= $item['compra_id']; ?>" class="btn btn-success">Adicionar</a>

                    </td>


                </tr>

            <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="modalFornecedores" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Últimas compras de <span id="fornecedorNome"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th data-label="Fornecedor">Fornecedor</th>
                                <th data-label="Data da Compra">Data da Compra</th>
                                <th data-label="Data da Entrada">Data da Entrada</th>
                                <th data-label="Valor Total">Valor Total</th>
                            </tr>
                        </thead>
                        <tbody id="modalFornecedoresBody">
                            <!-- Conteúdo via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    const API_URL = "<?= API_URL ?>";
</script>

<script src="<?= BASEJS ?>/modal_fornecedores.js"></script>