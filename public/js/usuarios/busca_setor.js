console.log("busca_setor.js carregado!");
$(document).ready(function() {
    const $grupoSelect = $('#grupo_id');
    const $setorSelect = $('#setor_id');

    const setorSalvo = $grupoSelect.data('setor-salvo') || null;

    function carregarSetores(grupo_id, selecionarSetor = null) {
        $setorSelect.prop('disabled', true);
        $setorSelect.html('<option disabled selected value="">Carregando...</option>');

        if (!grupo_id) {
            $setorSelect.html('<option disabled selected value="">Selecione o setor</option>');
            $setorSelect.prop('disabled', true);
            return;
        }

        $.getJSON(`${API_URL}/usuarios/busca_setor.php`, { grupo_id: grupo_id })
            .done(function(data) {
                $setorSelect.html('<option disabled selected value="">Selecione o setor</option>');

                if (data.length > 0) {
                    data.forEach(setor => {
                        const $option = $('<option>', {
                            value: setor.setor_id,
                            text: setor.setor_nome
                        });

                        // seleciona o setor salvo se existir
                        if (selecionarSetor && setor.setor_id == selecionarSetor) {
                            $option.prop('selected', true);
                        }

                        $setorSelect.append($option);
                    });

                    // Se houver apenas 1 opção real (excluindo o "Selecione o setor"), já seleciona
                    if (data.length === 1 && !selecionarSetor) {
                        $setorSelect.val(data[0].setor_id);
                    }

                    $setorSelect.prop('disabled', false);

                } else {
                    $setorSelect.append('<option disabled>Nenhum setor encontrado</option>');
                    $setorSelect.prop('disabled', true);
                }
            })
            .fail(function() {
                $setorSelect.html('<option disabled>Erro ao carregar setores</option>');
                $setorSelect.prop('disabled', true);
            });
    }

    // Carrega setores ao abrir a página
    if ($grupoSelect.val()) {
        carregarSetores($grupoSelect.val(), setorSalvo);
    }

    $grupoSelect.on('change', function() {
        carregarSetores($(this).val());
    });

    
});


