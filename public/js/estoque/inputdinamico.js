console.log("inputdinamico.js (estoque) carregado!");

$(document).ready(function () {
    let i = 2; // Inicializa a variável i com 2, pois o primeiro conjunto já existe

    // Função para verificar estoque e alerta
    function verificarEstoque(alerta, estoque, quantidade, estoqueElement) {
        // Calcula o estoque após subtração
        var estoqueAposSubtracao = estoque - quantidade;

        // Verifica se o estoque após subtração é igual ou menor que o alerta
        if (estoqueAposSubtracao <= alerta) {
            estoqueElement.classList.add('is-invalid');
        } else {
            estoqueElement.classList.remove('is-invalid');
        }
    }


    // Função para inicializar a verificação
    function inicializarVerificacao() {
        var alerta = parseInt(document.getElementById('alerta_1').value);
        var estoque = parseInt(document.getElementById('estoque_1').value);
        var quantidade = parseInt(document.getElementById('quantidade_1').value) || 0;

        verificarEstoque(alerta, estoque, quantidade, document.getElementById('estoque_1'));
    }


    // Event listeners para os campos principais
    document.getElementById('alerta_1').addEventListener('input', inicializarVerificacao);
    document.getElementById('estoque_1').addEventListener('input', inicializarVerificacao);
    document.getElementById('quantidade_1').addEventListener('input', inicializarVerificacao);

    // Adicionar campo
    $("#adicionar").click(function () {
        $("#produtos").append(
            '<div class="produto row g-2" id="saida_' + i + '">' +
            '<div class="col-md-5">' +
            '<div class="form-floating">' +
            '<input type="text" class="form-control produtos" name="produto[]" id="produto_' + i + '" required />' +
            '<label for="produto_' + i + '">Produto:</label>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<div class="form-floating">' +
            '<input type="text" class="form-control quantidade" name="quantidade[]" id="quantidade_' + i + '" required />' +
            '<label for="quantidade_' + i + '">Quantidade:</label>' +
            '</div>' +
            '</div>' +

            '<div class="col-md-2">' +
            '<div class="form-floating">' +
            '<input readonly type="text" class="form-control alerta" name="alerta[]" id="alerta_' + i + '" required />' +
            '<label for="alerta_' + i + '">Alerta:</label>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<div class="form-floating">' +
            '<input readonly type="text" class="form-control estoque" name="estoque[]" id="estoque_' + i + '" required />' +
            '<label for="estoque_' + i + '">Estoque:</label>' +
            '</div>' +
            '</div>' +

            '<input type="hidden" class="form-control" name="produto_id[]" id="produto_id_' + i + '" required />' +
            '<input type="hidden" class="form-control" name="medida_id[]" id="medida_id_' + i + '" required />' +
            '<input type="hidden" class="form-control" name="preco[]" id="preco_' + i + '" required />' +
            '<input type="hidden" class="form-control" name="prioridade[]" id="prioridade_' + i + '" required />' +
            '<input type="hidden" class="form-control" name="codigo[]" id="codigo_' + i + '" required />' +


            '<div class="col-md-1 d-flex align-items-center">' +
            '<button type="button" class="btn-remove remover">Remover</button>' +
            '</div>' +
            '</div>'
        );


        // Reaplica o autocomplete no novo campo
        aplicarAutocomplete('#produto_' + i);
        // Adicionar evento para limpar produto_id ao alterar produto
        monitorarAlteracaoProduto('#produto_' + i, '#produto_id_' + i, '#alerta_' + i, '#quantidade_' + i, '#estoque_' + i);


        i++; // Incrementa a variável i para a próxima iteração



        // Verificação ao alterar a quantidade nos campos dinamicamente adicionados
        $(document).on('input', '.quantidade', function () {
            const row = $(this).closest('.row');
            const alerta = parseInt(row.find('input[name^="alerta"]').val());
            const estoque = parseInt(row.find('input[name^="estoque"]').val());
            const quantidade = parseInt($(this).val()) || 0;

            verificarEstoque(alerta, estoque, quantidade, row.find('input[name^="estoque"]')[0]);
        });

        $(document).on('input', 'input[name^="alerta"], input[name^="estoque"]', function () {
            const row = $(this).closest('.row');
            const alerta = parseInt(row.find('input[name^="alerta"]').val());
            const estoque = parseInt(row.find('input[name^="estoque"]').val());
            const quantidade = parseInt(row.find('input[name^="quantidade"]').val()) || 0;

            verificarEstoque(alerta, estoque, quantidade, row.find('input[name^="estoque"]')[0]);
        });
    });


    // Remover campo
    $(document).on('click', '.remover', function () {
        $(this).closest('.produto').remove();
        //calcularTotal(); // Recalcula o total após a remoção de um campo
    });

});
