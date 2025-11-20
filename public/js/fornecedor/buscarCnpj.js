// Opcional: armazene o CNPJ original ao carregar a página:
// <input type="hidden" id="fornecedor_cnpj_original" value="<?= preg_replace('/\D/','', $fornecedor_cnpj ?? '') ?>">

$('#fornecedor_cnpj').on('blur', function () {
    const raw = $(this).val().trim();
    if (!raw) return; // vazio -> nada

    // limpa para só números (ex: 61.092.680/0001-05 -> 61092680000105)
    const cnpjClean = raw.replace(/\D/g, '');
    if (!cnpjClean) return;

    // pega id do fornecedor (0 se for novo)
    const fornecedorId = parseInt($('#fornecedor_id').val(), 10) || 0;

    // Se tiver original e não mudou, não validamos
    const original = $('#fornecedor_cnpj_original').val() ? $('#fornecedor_cnpj_original').val().replace(/\D/g,'') : '';
    if (original && original === cnpjClean) {
        // garante que esteja sem erro se era o próprio CNPJ
        $('#fornecedor_cnpj').removeClass('is-invalid');
        $('#submitBtn, #editSubmitBtn').prop('disabled', false);
        return;
    }

    // DEBUG: mostra o que será enviado
    console.log('Validando CNPJ:', cnpjClean, 'fornecedor_id:', fornecedorId);

    $.ajax({
        url: `${API_URL}/buscarCnpj.php`,
        method: 'POST',
        dataType: 'json',
        data: {
            fornecedor_cnpj: cnpjClean,
            fornecedor_id: fornecedorId
        },
        success: function (response) {
            console.log('Resposta buscarCnpj:', response);

            // Se a API retornou erro, não bloqueia o formulário — só loga
            if (response.error) {
                console.error('API error:', response.error);
                // opcional: mostrar mensagem amigável ao usuário
                $('#fornecedor_cnpj').removeClass('is-invalid');
                $('#submitBtn, #editSubmitBtn').prop('disabled', false);
                return;
            }

            // Normal: existe -> bloquear; não existe -> liberar
            if (response.exists === true) {
                $('#fornecedor_cnpj').addClass('is-invalid');
                $('#submitBtn, #editSubmitBtn').prop('disabled', true);
                // opcional: mostrar texto com invalid-feedback
                if ($('#fornecedor_cnpj_feedback').length) {
                    $('#fornecedor_cnpj_feedback').text('CNPJ já cadastrado em outro fornecedor').show();
                }
            } else {
                $('#fornecedor_cnpj').removeClass('is-invalid');
                $('#submitBtn, #editSubmitBtn').prop('disabled', false);
                if ($('#fornecedor_cnpj_feedback').length) {
                    $('#fornecedor_cnpj_feedback').hide();
                }
            }
        },
        error: function (xhr, status, err) {
            console.error('Erro na requisição buscarCnpj:', status, err);
            // não travar o formulário por erro de rede
            $('#fornecedor_cnpj').removeClass('is-invalid');
            $('#submitBtn, #editSubmitBtn').prop('disabled', false);
        }
    });
});
