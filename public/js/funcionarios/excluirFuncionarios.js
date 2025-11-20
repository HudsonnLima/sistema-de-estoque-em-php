console.log("excluirFuncionarios.js carregado!");
$(document).ready(function () {
    var excluirModal = null;
    if ($('#modalExcluirFuncionario').length === 0) {
        const modalMarkup = `
        <div class="modal fade" id="modalExcluirFuncionario" tabindex="-1" aria-labelledby="modalExcluirFuncionarioLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirFuncionarioLabel">Confirmar exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div id="modalDetalheFuncionario"></div>
                <hr>
                Tem certeza que deseja excluir este funcionário?
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="confirmarExclusao">Excluir</button>
              </div>
            </div>
          </div>
        </div>`;
        $('body').append(modalMarkup);
        excluirModal = new bootstrap.Modal(document.getElementById('modalExcluirFuncionario'), { backdrop: 'static', keyboard: false });
    } else {
        excluirModal = new bootstrap.Modal(document.getElementById('modalExcluirFuncionario'), { backdrop: 'static', keyboard: false });
    }

    let userIdToDelete = null;

    $(document).on('click', '.excluirUsuario', function (e) {
        e.preventDefault();
        userIdToDelete = $(this).data('id');
        const row = $(this).closest('tr');
        const nome = row.find('td').eq(0).text().trim().replace(/\s+/g, ' ');
        const cargo = row.find('td').eq(1).text().trim();

        $('#modalDetalheFuncionario').html(`
            <p><strong>Usuário ID:</strong> ${userIdToDelete}</p>
            <p><strong>Nome:</strong> ${nome}</p>
            <p><strong>Cargo:</strong> ${cargo}</p>
        `);

        excluirModal.show();
    });

    $(document).on('click', '#confirmarExclusao', function (e) {
        e.preventDefault();
        if (!userIdToDelete) return;

        $.ajax({
            url: `${API_URL}/funcionarios/excluirFuncionario.php`,
            method: 'POST',
            dataType: 'json',
            data: { user_id: userIdToDelete },
            success: function (res) {

                $('.alert').remove();

                const type = res.status ? 'success' : 'danger';
                const alertHtml = `<div class="alert alert-${type}">${res.mensagem}</div>`;
                $('form#usuarios').before(alertHtml);

                // ⬅ ADICIONADO — sobe a página pro topo como em compras
                window.scrollTo({ top: 0 });

                if (res.status) {
                    $(`a.excluirUsuario[data-id="${userIdToDelete}"]`).closest('tr').remove();
                    excluirModal.hide();
                    userIdToDelete = null;
                }

                setTimeout(() => $('.alert').fadeOut(400, function(){ $(this).remove(); }), 3500);
            },
            error: function (xhr) {

                $('.alert').remove();
                const text = xhr.responseText || 'Erro na requisição';
                const alertHtml = `<div class="alert alert-danger">${text}</div>`;
                $('form#usuarios').before(alertHtml);

                // ⬅ ADICIONADO — sobe a página mesmo em erro
                window.scrollTo({ top: 0 });

                setTimeout(() => $('.alert').fadeOut(400, function(){ $(this).remove(); }), 3500);
            }
        });
    });
});
