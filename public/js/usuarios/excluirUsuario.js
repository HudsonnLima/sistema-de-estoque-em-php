console.log("excluirUsuario.js carregado!");

$(document).ready(function () {
  let excluirModal = null;
  let usuarioIdToDelete = null;

  // Cria modal de exclusão, se ainda não existir
  if ($('#modalExcluirUsuario').length === 0) {
    const modalMarkup = `
      <div class="modal fade" id="modalExcluirUsuario" tabindex="-1" aria-labelledby="modalExcluirUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalExcluirUsuarioLabel">Confirmar exclusão</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div id="modalDetalheUsuario"></div>
              <hr>
              Tem certeza que deseja excluir o usuário?
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button class="btn btn-danger" id="confirmarExclusao">Excluir</button>
            </div>
          </div>
        </div>
      </div>`;
    $('body').append(modalMarkup);
  }

  excluirModal = new bootstrap.Modal(document.getElementById('modalExcluirUsuario'), { backdrop: 'static', keyboard: false });

  // Ao clicar no botão de exclusão
  $(document).on('click', '.excluirUsuario', function (e) {
    e.preventDefault();

    usuarioIdToDelete = $(this).data('id');

    // Pega o nome do usuário (assumindo que está na primeira coluna da tabela)
    const row = $(this).closest('tr');
    const userName = row.find('td').eq(0).text().trim();

    // Exibe o nome na modal
    $('#modalDetalheUsuario').html(`
      <p><strong>Usuário:</strong> ${userName}</p>
    `);

    excluirModal.show();
  });

  // Confirma exclusão
  $(document).on('click', '#confirmarExclusao', function (e) {
    e.preventDefault();
    if (!usuarioIdToDelete) return;

    $.ajax({
      url: `${API_URL}/usuarios/excluirUsuario.php`,
      method: 'POST',
      dataType: 'json',
      data: { user_id: usuarioIdToDelete },
      success: function (res) {
        // Remove alertas anteriores
        $('.alert').remove();

        // Mostra alerta de sucesso ou erro
        const type = res.status ? 'success' : 'danger';
        const alertHtml = `<div class="alert alert-${type}">${res.mensagem}</div>`;
        $('form#usuarios').before(alertHtml);

        // Se sucesso, remove linha da tabela e fecha modal
        if (res.status) {
          $(`a.excluirUsuario[data-id="${usuarioIdToDelete}"]`).closest('tr').remove();
          excluirModal.hide();
          usuarioIdToDelete = null;
        }

        // Remove alerta após 3,5s
        setTimeout(() => $('.alert').fadeOut(400, function () {
          $(this).remove();
        }), 3500);
      },
      error: function (xhr) {
        $('.alert').remove();
        const text = xhr.responseText || 'Erro na requisição.';
        const alertHtml = `<div class="alert alert-danger">${text}</div>`;
        $('form#usuarios').before(alertHtml);
        setTimeout(() => $('.alert').fadeOut(400, function () {
          $(this).remove();
        }), 3500);
      }
    });
  });
});
