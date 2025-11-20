<!-- Modal Editar Compra -->
<div class="modal fade" id="modalEditarCompra" tabindex="-1" aria-labelledby="modalEditarCompraLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalEditarCompraLabel">Editar Dados da Compra</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="formEditarCompra" method="POST">
        <div class="modal-body">
          <input type="hidden" name="compra_id" id="edit_id">

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_fornecedor" name="fornecedor_id" readonly>
                <label for="edit_fornecedor">Fornecedor:</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="edit_pagamento" name="pagamento_id">
                <label for="edit_pagamento">Pagamento:</label>
              </div>
            </div>
          </div>

          <div class="row g-2 mb-3">
            
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="data" name="cad_data">
                <label for="edit_data">Data da compra:</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" class="form-control" id="data_entrega" name="previsao">
                <label for="edit_previsao">Previsão de entrega:</label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="submitBtnEditHeader" class="btn btn-primary" id="submitBtnModal" >Salvar Alterações</button>
          
        </div>
      </form>

    </div>
  </div>
</div>
