console.log("autocomplete.js (estoque) carregado!");

// === FUNÇÃO AUTOCOMPLETE ===
function aplicarAutocomplete(selector) {
    $(selector).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: `${API_URL}/estoque/autocomplete.php`,
                type: 'post',
                dataType: "json",
                data: { busca: request.term },
                success: function (data) {
                    response(data);
                }
            });
        },
        focus: function (event, ui) {
            // Exibe apenas o nome ao navegar nas opções
            $(this).val(ui.item.label);
            return false;
        },
        select: function (event, ui) {
            const $input = $(this);
            const row = $input.closest('.row');

            // Preenche os campos ocultos e visíveis correspondentes
            row.find('input[name^="produto_id"]').val(ui.item.produto_id);
            row.find('input[name^="medida_id"]').val(ui.item.medida_id);
            row.find('input[name^="estoque"]').val(ui.item.estoque);
            row.find('input[name^="preco"]').val(ui.item.preco);
            row.find('input[name^="codigo"]').val(ui.item.codigo);
            row.find('input[name^="alerta"]').val(ui.item.alerta);
            row.find('input[name^="prioridade"]').val(ui.item.prioridade);

            // Atualiza o campo principal
            $input.val(ui.item.label);

            // Remove erro visual e habilita botão
            $input.removeClass('is-invalid');
            $input.next('.alerta-exclamacao').remove();
            $('#submitBtn').prop('disabled', false);

            return false;
        }
    });
}


// === MONITORAR ALTERAÇÃO DE PRODUTO ===
function monitorarAlteracaoProduto(produtoSelector, produtoIdSelector, alertaSelector, quantidadeSelector, estoqueSelector) {

    // Sempre que o usuário alterar o texto do produto
    $(document).on('input', produtoSelector, function () {
        const row = $(this).closest('.row');

        // Limpa todos os campos relacionados
        row.find(produtoIdSelector).val('');
        row.find(alertaSelector).val('');
        row.find(quantidadeSelector).val('');
        row.find(estoqueSelector).val('');
        row.find('input[name^="preco"]').val('');
        row.find('input[name^="codigo"]').val('');
        row.find('input[name^="prioridade"]').val('');
        row.find('input[name^="medida_id"]').val('');
    });

    // Ao sair do campo, valida se produto_id foi preenchido
    $(document).on('blur', produtoSelector, function () {
        const row = $(this).closest('.row');
        const produtoId = row.find(produtoIdSelector).val();

        if (!produtoId) {
            $(this).addClass('is-invalid');
            $(this).next('.alerta-exclamacao').remove();
            $('#submitBtn').prop('disabled', true);
        } else {
            $(this).removeClass('is-invalid');
            $('#submitBtn').prop('disabled', false);
        }
    });
}





// === INICIALIZAÇÃO PADRÃO ===
aplicarAutocomplete('#produto_1');
monitorarAlteracaoProduto('#produto_1', '#produto_id_1', '#alerta_1', '#quantidade_1', '#estoque_1');
