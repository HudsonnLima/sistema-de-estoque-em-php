console.log("inputdinamico.js carregado!");
$(document).ready(function () {
    let i = 2; // Inicializa a variável i com 2, pois o primeiro conjunto já existe



    // Adicionar campo
    $("#adicionar").click(function () {
        $("#produtos").append(
            '<div class="produto row g-2" id="saida_' + i + '">' +
            '<div class="col-md-4">' +
            '<div class="form-floating">' +
            '<input type="text" class="form-control produtos" name="produto[]" id="produto_' + i + '" required />' +
            '<label for="produto_' + i + '">Produto:</label>' +
            '</div>' +
            '</div>' +
            '<input type="hidden" class="form-control" name="produto_id[]" id="produto_id_' + i + '" required />' +
            '<input type="hidden" class="form-control" name="medida_id[]" id="medida_id_' + i + '" required />' +
            '<input type="hidden" class="form-control" name="codigo[]" id="codigo_' + i + '" required />' +
            '<div class="col-md-2">' +
            '<div class="form-floating">' +
            '<input type="text" class="form-control quantidade" name="quantidade[]" id="quantidade_' + i + '" required />' +
            '<label for="quantidade_' + i + '">Quantidade:</label>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<div class="form-floating">' +
            '<input type="text" class="form-control preco" name="preco[]" id="preco_un_' + i + '" required />' +
            '<label for="preco_un_' + i + '">Preço UN:</label>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<div class="form-floating">' +
            '<input type="text" class="form-control preco total" name="" id="preco_tt_' + i + '" required />' +
            '<label for="preco_tt_' + i + '">Preço Total:</label>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-1">' +
            '<div class="form-floating">' +
            '<input readonly type="text" class="form-control" name="estoque[]" id="estoque_' + i + '" required />' +
            '<label for="estoque_' + i + '">Estoque:</label>' +
            '</div>' +
            '</div>' +
            '<br />' +
            '<div class="col-md-1 d-flex align-items-center">' +
            '<button type="button" class="btn-remove remover">Remover</button>' +
            '</div>' +
            '</div>'
        );

        // Aplicar a máscara de moeda nos novos campos criados
        aplicarMascaraMoeda('#preco_un_' + i);
        aplicarMascaraMoeda('#preco_tt_' + i);

        // Reaplica o autocomplete no novo campo
        aplicarAutocomplete('#produto_' + i);
        // Adicionar evento para limpar produto_id ao alterar produto
        monitorarAlteracaoProduto('#produto_' + i, '#produto_id_' + i);

        i++; // Incrementa a variável i para a próxima iteração
    });

    // Remover campo
    $(document).on('click', '.remover', function () {
        $(this).closest('.produto').remove();
        calcularTotal(); // Recalcula o total após a remoção de um campo
    });

    // Calcular total
    $(document).on('input', '.quantidade, .preco, .total', function () {
        calcularTotal(); // Recalcula o total quando quantidade, preço ou total são alterados
    });

        



});
