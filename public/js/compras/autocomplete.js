console.log("autocomplete.js carregado!");
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

                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $(this).val(ui.item.label);
            const row = $(this).closest('.row');
            console.log(row.find('input[name^="codigo"]')); // TESTE

            row.find('input[name^="produto_id"]').val(ui.item.produto_id);
            row.find('input[name^="medida_id"]').val(ui.item.medida_id);
            row.find('input[name^="estoque"]').val(ui.item.estoque);
            row.find('input[name^="codigo"]').val(ui.item.codigo);

            $(this).removeClass('is-invalid');
            $(this).next('.alerta-exclamacao').remove();
            $('#submitBtn').prop('disabled', false);

            console.log("Valores após autocomplete:");
            console.log("produto_id:", row.find('input[name^="produto_id"]').val());
            console.log("codigo:", row.find('input[name^="codigo"]').val());
            console.log("medida_id:", row.find('input[name^="medida_id"]').val());


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
            row.find('input[name^="codigo"]').val(ui.item.codigo);



            return false;
        }

    })
}


function monitorarAlteracaoProduto(produtoSelector, produtoIdSelector) {
    $(document).on('input', produtoSelector, function () {
        $(produtoIdSelector).val(''); // Limpa o campo produto_id
    });

    $(document).on('blur', produtoSelector, function () {
        const row = $(this).closest('.row');
        let campoInvalido = false;

        // Verifica apenas os campos ocultos essenciais
        const camposParaValidar = [
            'input[name^="produto_id"]',
            'input[name^="codigo"]',
            'input[name^="medida_id"]'
        ];

        camposParaValidar.forEach(selector => {
            row.find(selector).each(function () {
                const val = $(this).val();
                const idAttr = $(this).attr('id') || '';
                // Aplica is-invalid se o valor estiver vazio ou se o id estiver no padrão errado
                if (!val || !/^produto_id_|^codigo_|^medida_id_/.test(idAttr)) {
                    campoInvalido = true;
                    console.log("Campo oculto inválido:", this); // Mostra o campo problemático
                }
            });
        });

        if (campoInvalido) {
            $(this).addClass('is-invalid');
            $(this).next('.alerta-exclamacao').remove();
            $('#submitBtn').prop('disabled', true);
        } else {
            $(this).removeClass('is-invalid');
            $('#submitBtn').prop('disabled', false);
        }
    });
}





// Função para validar campos text vazios ou sem id
function validarCamposVazios(produtoSelector) {
    $(document).on('blur', produtoSelector, function () {
        const row = $(this).closest('.row');
        let campoInvalido = false;

        // Percorre todos os inputs type="text" dentro da row
        row.find('input[type="text"]').each(function () {
            if (!$(this).val().trim() || $(this).attr('id') === '') {
                campoInvalido = true;
                return false; // sai do each
            }
        });

        if (campoInvalido) {
            $(this).addClass('is-invalid');
            $(this).next('.alerta-exclamacao').remove(); // remove alerta antigo se existir
            $('#submitBtn').prop('disabled', true);
        } else {
            $(this).removeClass('is-invalid');
            $('#submitBtn').prop('disabled', false);
        }
    });
}

// Aplicar a validação ao campo de produto
validarCamposVazios('#produto_1');


// Aplicar autocomplete no primeiro campo de produto
aplicarAutocomplete('#produto_1');

// Monitorar alterações no primeiro campo de produto
monitorarAlteracaoProduto('#produto_1', '#produto_id_1');