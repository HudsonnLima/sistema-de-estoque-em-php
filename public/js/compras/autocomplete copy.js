// Função para aplicar autocomplete
function aplicarAutocomplete(selector) {
    $(selector).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: `${API_URL}/autocomplete.php`,
                type: 'post',
                dataType: "json",
                data: {
                    busca: request.term,
                    //empresa_id: $('#empresa_id').val()
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $(this).val(ui.item.label);
            const row = $(this).closest('.row');
            row.find('input[name^="produto_id"]').val(ui.item.produto_id);
            row.find('input[name^="medida_id"]').val(ui.item.medida_id);
            row.find('input[name^="estoque"]').val(ui.item.estoque);

            // Remove borda vermelha ao selecionar item válido
            $(this).removeClass('is-invalid');
            $(this).next('.alerta-exclamacao').remove();
            $('#submitBtn').prop('disabled', false);

            return false;
        },
        focus: function (event, ui) {
            $(this).val(ui.item.label);
            return false;
        },

        //EXIBE O RESULTADO EM TODOS OS CAMPOS ENQUANDO NAVEGA 
        focus: function (event, ui) {
            // Preenche os campos temporariamente ao focar em uma sugestão
            $(this).val(ui.item.label);
            const row = $(this).closest('.row');
            row.find('input[name^="produto_id"]').val(ui.item.produto_id);
            row.find('input[name^="medida_id"]').val(ui.item.medida_id);
            row.find('input[name^="estoque"]').val(ui.item.estoque);

            return false;
        }
        
    })
}

// Função para monitorar alterações no campo produto
function monitorarAlteracaoProduto(produtoSelector, produtoIdSelector) {
    $(document).on('input', produtoSelector, function () {
        $(produtoIdSelector).val(''); // Limpa o campo produto_id
    });

    $(document).on('blur', produtoSelector, function () {
        if (!$(produtoIdSelector).val()) { // Verifica se produto_id está vazio
            // Adiciona a classe para borda vermelha e exibe o alerta
            $(this).addClass('is-invalid');

            // Remove qualquer alerta existente antes de adicionar
            $(this).next('.alerta-exclamacao').remove();

            // Desabilita o botão de submit
            $('#submitBtn').prop('disabled', true);
        }
    });
}

// Aplicar autocomplete no primeiro campo de produto
aplicarAutocomplete('#produto_1');

 // Monitorar alterações no primeiro campo de produto
 monitorarAlteracaoProduto('#produto_1', '#produto_id_1');