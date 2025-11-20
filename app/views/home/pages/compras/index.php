   <!-- Inclua o jQuery UI depois -->
   <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
   <!-- Inclua o CSS do jQuery UI, se necessário -->
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">

   <?php

    use App\Core\Database;
    use App\Models\Read;
    use App\Controllers\ControllerCompras;
    use App\Models\EmailCompras;

    $db = new Database();
    $pdo = $db->getConnection();

    $controller = new ControllerCompras($pdo);
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


   <label class="titulo">Cadastrar nova compra:</label>
   <hr>

   <form method="POST" id="compras" enctype="multipart/form-data">

       <input type="hidden" class="form-control" name="compra_id" id="" value="<?php echo rand(1, 999) . date('Hi'); ?>" />

       <div class="row g-2">



           <div class="col-md-5">
               <div class="form-floating ">
                   <select class="form-select" name="fornecedor_id" id="fornecedor_id">
                       <?php
                        $data = "SELECT * FROM fornecedor ORDER BY fornecedor_razao ASC";
                        $stmt = $pdo->prepare($data);
                        $stmt->execute();
                        echo "<option disabled selected value=''>Selecione o fornecedor</option>";
                        foreach ($stmt as $fornecedor) :
                        ?>
                           <option value="<?php echo $fornecedor['fornecedor_id']; ?>"
                               <?php if (isset($dados['fornecedor_id']) and $dados['fornecedor_id'] == $fornecedor['fornecedor_id']): echo "selected='select'";
                                endif; ?>>
                               <?php echo $fornecedor['fornecedor_razao']; ?>
                           </option>
                       <?php endforeach; ?>
                   </select>
                   <label for="floatingSelectGrid">Fornecedor:</label>
               </div>
           </div>


           <div class="col-md-3">
               <div class="form-floating ">
                   <select class="form-select" name="pagamento_id" id="" aria-label="">
                       <option selected disabled="disabled" value=""> Condição de pagamento: </option>
                       <option value="0">À vista</option>
                       <option value="1">1x</option>
                       <option value="2">2x</option>
                       <option value="3">3x</option>
                       <option value="4">4x</option>
                       <option value="5">5x</option>
                       <option value="6">6x</option>
                       <option value="7">7x</option>
                       <option value="8">8x</option>
                       <option value="9">9x</option>
                       <option value="10">10x</option>
                       <option value="11">11x</option>
                       <option value="12">12x</option>


                   </select>
                   <label for="floatingSelectGrid">Condição de pagamento:</label>
               </div>
           </div>

           <div class="col-md-2">
               <div class="form-floating">
                   <input type="date" name="previsao" class="form-control" id="data_entrega" required value="" />
                   <label for="floatingInputGrid">Previsao de entrega:</label>
               </div>
           </div>

           <div class="col-md-2">
               <div class="form-floating">
                   <input type="date" name="cad_data" class="form-control" id="data" required value="<?php if (isset($dados['cad_data'])) : echo $dados['cad_data'];
                                                                                                        else : echo date('Y-m-d');
                                                                                                        endif; ?>" />
                   <label for="floatingInputGrid">Data da compra:</label>
               </div>
           </div>

           <div id="produtos">
               <div class="row g-2 produto" id="saida_1">
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="text" class="form-control produtos" name="produto[]" id="produto_1" value="<?php if (isset($dados['produto'][''])) echo $dados['produto']; ?>" required />
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
                           <input disabled type="text" class="form-control" name="estoque[]" id="estoque_1" value="" required />
                           <label for="estoque_1">Estoque:</label>
                       </div>
                   </div>
                   <input type="hidden" class="form-control" name="cad_autor" id="" value="<?= "{$_SESSION['user_id']}"; ?>" />

                   <div class="col-md-1 d-flex align-items-center">
                       <button type="button" id="adicionar" class="btn-add">+Produtos</button>
                   </div>
               </div>

           </div>
           <br />
       </div>
       <br />
       <input type="submit" name="submitBtn" id="submitBtn" value="Cadastrar" class="btn btn-success" />

   </form>

   <br />

   <?php

    $read = new Read();


    $query = "
    SELECT c.* FROM compras c
    INNER JOIN (SELECT compra_id, MIN(id) AS min_id FROM compras WHERE estado = 0 
        GROUP BY compra_id) AS sub ON sub.min_id = c.id ORDER BY c.produto ASC
";
    $compras = $read->fetchAll($query);


    // Busca principal das compras
    /*
    $query = "SELECT * FROM compras WHERE estado = 0 GROUP BY compra_id ORDER BY produto ASC";
    $compras = $read->fetchAll($query);
*/
    if ($read->getRowCount() <= 0) :
        echo "<br/><div class='trigger alert'>Não há compras cadastradas!</div>";
    else :
    ?>
       <table id="search_table" class="table table-striped">
           <thead>
               <tr class="table-dark">
                   <th class="text-start">Fornecedor</th>
                   <th class="text-start">Comprado por</th>
                   <th class="text-start">R$ - Total</th>
                   <th class="text-center">Data da compra</th>
                   <th class="text-center">Prev - Entrega</th>
                   <th class="text-center">Ação</th>
               </tr>
           </thead>
           <tbody>
               <?php
                foreach ($compras as $compra) :

                    $fornecedor = $read->fetch(
                        "SELECT fornecedor_razao FROM fornecedor WHERE fornecedor_id = :fid ORDER BY fornecedor_razao ASC",
                        ['fid' => $compra['fornecedor_id']]
                    );
                    $fornecedorNome = $fornecedor['fornecedor_razao'] ?? "Fornecedor não encontrado.";


                    $usuario = $read->fetch(
                        "SELECT user_name FROM users WHERE user_id = :uid",
                        ['uid' => $compra['cad_autor']]
                    );
                    $userNome = $usuario['user_name'] ?? "Usuário não encontrado.";


                    $soma = $read->fetch(
                        "SELECT SUM(quantidade * preco) AS total FROM compras WHERE compra_id = :cid",
                        ['cid' => $compra['compra_id']]
                    )['total'] ?? 0;

                    $hoje = new DateTime(date('Y-m-d'));
                    $previsao = new DateTime($compra['previsao']);

                    $cor = ($hoje < $previsao) ? 'green' : (($hoje > $previsao) ? 'red' : 'blue');

                ?>


                   <tr class="table-light">
                       <td data-label="Fornecedor">
                           <a href="#" data-id="<?= $compra['fornecedor_id']; ?>"
                               class="exibirfornecedor link"><?= htmlspecialchars($fornecedorNome); ?></a>
                       </td>

                       <td data-label="Comprado por"><?= htmlspecialchars($userNome); ?></td>

                       <td data-label="Total">R$: <?= number_format($soma, 2, ",", "."); ?></td>

                       <td data-label="Data da compra" class="text-center"><?= date('d/m/Y', strtotime($compra['cad_data'])); ?></td>

                       <td data-label="Previsão de entrega" style="color:<?= $cor ?>; font-weight:600;" class="text-center">
                           <?= date('d/m/Y', strtotime($compra['previsao'])); ?>
                       </td>

                       <td data-label="Ação" class="text-center">
                           <a href="<?= BASE_URL ?>/compras/editar?compraId=<?= $compra['compra_id']; ?>"><img src="<?= BASE_IMG ?>/edit.png" width="20"></a>
                           <a data-id="<?= $compra['compra_id']; ?>" class="excluircompra"><img src="<?= BASE_IMG ?>/del.png" width="20"></a>
                       </td>
                   </tr>
               <?php endforeach; ?>
           </tbody>
       </table>


   <?php


    endif;
    ?>




   <script>
       const API_URL = "<?= API_URL ?>";
   </script>

   <script src="<?= BASEJS ?>/compras/autocomplete.js"></script>
   <script src="<?= BASEJS ?>/compras/checkdate.js"></script>
   <script src="<?= BASEJS ?>/compras/inputdinamico.js"></script>
   <script src="<?= BASEJS ?>/compras/maskmoney.js"></script>
   <script src="<?= BASEJS ?>/compras/excluirCompra.js"></script>