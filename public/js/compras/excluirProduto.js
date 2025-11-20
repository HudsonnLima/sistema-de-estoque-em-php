$(document).ready(function () {
    let produtoIdParaExcluir = null;

    // Ao clicar no botão de excluir
    $('.excluirproduto').on('click', function (e) {
        e.preventDefault();
        produtoIdParaExcluir = $(this).data('id'); // pega o id do produto
        $('#modalExcluirProduto').modal('show');
    });

    // Confirmação de exclusão
    $('#confirmarExcluirProduto').on('click', function () {
        if (!produtoIdParaExcluir) return;
        $.ajax({
            url: `${API_URL}/excluirProduto.php`,
            type: 'POST',
            dataType: 'json',
            data: { produto_id: produtoIdParaExcluir },
            success: function (r) {
                if (r.sucesso) {
                    // Remove a linha da tabela (opcional)
                    $(`a[data-id="${produtoIdParaExcluir}"]`).closest('tr').fadeOut(300, function () {
                        $(this).remove();
                    });

                    // Recarrega a página para ler a $_SESSION
                    location.reload();
                } else {
                    // Pode exibir erro de outra forma ou recarregar também
                    location.reload();
                }

                $('#modalExcluirProduto').modal('hide');
                produtoIdParaExcluir = null;
            },


            error: function (jqXHR, textStatus, thrownError) {
                console.error('AJAX ERROR', textStatus, thrownError);
                console.error('Status:', jqXHR.status);
                console.error('Response:', jqXHR.responseText);
                alert('Erro ao conectar com o servidor.');
                $('#modalExcluirProduto').modal('hide');
                produtoIdParaExcluir = null;
            }
        });
    });
});
