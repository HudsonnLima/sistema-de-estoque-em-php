$(document).ready(function () {
    var excluirModal = null;
    if ($('#modalExcluirCompra').length === 0) {
        const modalMarkup = `
        <div class="modal fade" id="modalExcluirCompra" tabindex="-1" aria-labelledby="modalExcluirCompraLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirCompraLabel">Confirmar exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div id="modalDetalheCompra"></div>
                <hr>
                Tem certeza que deseja excluir esta compra?
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="confirmarExclusao">Excluir</button>
              </div>
            </div>
          </div>
        </div>`;
        $('body').append(modalMarkup);
        excluirModal = new bootstrap.Modal(document.getElementById('modalExcluirCompra'), { backdrop: 'static', keyboard: false });
    } else {
        excluirModal = new bootstrap.Modal(document.getElementById('modalExcluirCompra'), { backdrop: 'static', keyboard: false });
    }

    let compraIdToDelete = null;

    $(document).on('click', '.excluircompra', function (e) {
        e.preventDefault();
        compraIdToDelete = $(this).data('id');
        const row = $(this).closest('tr');
        const fornecedor = row.find('td').eq(0).text().trim().replace(/\s+/g, ' ');
        const dataCompra = row.find('td').eq(3).text().trim();

        $('#modalDetalheCompra').html(`
            <p><strong>Compra ID:</strong> ${compraIdToDelete}</p>
            <p><strong>Fornecedor:</strong> ${fornecedor}</p>
            <p><strong>Data da compra:</strong> ${dataCompra}</p>
        `);

        excluirModal.show();
    });

    $(document).on('click', '#confirmarExclusao', function (e) {
        e.preventDefault();
        if (!compraIdToDelete) return;

        $.ajax({
            url: `${API_URL}/compras/excluirCompra.php`,
            method: 'POST',
            dataType: 'json',
            data: { compra_id: compraIdToDelete },
            success: function (res) {
                // Remove qualquer alert anterior
                $('.alert').remove();

                const type = res.status ? 'success' : 'danger';
                const alertHtml = `<div class="alert alert-${type}">${res.mensagem}</div>`;
                $('form#compras').before(alertHtml);

                if (res.status) {
                    $(`a.excluircompra[data-id="${compraIdToDelete}"]`).closest('tr').remove();
                    excluirModal.hide();
                    compraIdToDelete = null;
                }

                setTimeout(() => $('.alert').fadeOut(400, function(){ $(this).remove(); }), 3500);
            },
            error: function (xhr) {
                $('.alert').remove();
                const text = xhr.responseText || 'Erro na requisição';
                const alertHtml = `<div class="alert alert-danger">${text}</div>`;
                $('form#compras').before(alertHtml);
                setTimeout(() => $('.alert').fadeOut(400, function(){ $(this).remove(); }), 3500);
            }
        });
    });
});
