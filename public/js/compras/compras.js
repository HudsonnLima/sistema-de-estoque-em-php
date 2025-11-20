$(document).ready(function() {
  $('.btnEditarCompra').on('click', function(e) {
    e.preventDefault();

    const compra_id = $(this).data('id');
    $('#formEditarCompra')[0].reset();
    $('#edit_id').val(compra_id);
    $('#modalEditarCompra').modal('show');

    $.ajax({
      url: `${API_URL}/buscarCompra.php`,
      type: 'POST',
      dataType: 'json',
      data: { compra_id: compra_id },
      success: function(r) {
        if (r.sucesso) {
          $('#edit_fornecedor').val(r.fornecedor);
          $('#edit_pagamento').val(r.pagamento);
          $('#edit_usuario').val(r.usuario);
          $('#data').val(r.data);
          $('#data_entrega').val(r.previsao);
        } else {
          console.warn('API respondeu com sucesso=false', r);
          alert('Compra não encontrada ou erro ao buscar dados.');
          $('#modalEditarCompra').modal('hide');
        }
      },
      error: function(jqXHR, textStatus, thrownError) {
        // debug detalhado
        console.error('AJAX ERROR', textStatus, thrownError);
        console.error('Status:', jqXHR.status);
        console.error('Response:', jqXHR.responseText);
        alert('Erro ao conectar com o servidor. Veja console para detalhes.');
        $('#modalEditarCompra').modal('hide');
      }
    });
  });
});




