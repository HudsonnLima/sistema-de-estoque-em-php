$(document).on('click', '.abrir-modal-fornecedor', function(e) {
    e.preventDefault();

    const fornecedor_id = $(this).data('id');
    const fornecedor_nome = $(this).data('nome');

    $('#fornecedorNome').text(fornecedor_nome);

    const tbody = $('#modalFornecedoresBody');
    tbody.html('<tr><td colspan="4" class="text-center">Carregando...</td></tr>');

    $.ajax({
        url: `${API_URL}/relatorios/_modal_fornecedores.php`,
        method: 'GET',
        data: { fornecedor_id },
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                tbody.html(`<tr><td colspan="4" class="text-center text-danger">${data.error}</td></tr>`);
                return;
            }

            if (data.length === 0) {
                tbody.html('<tr><td colspan="4" class="text-center">Nenhum registro encontrado</td></tr>');
                return;
            }

            const rows = data.map(item => {
                // ✅ Formata valor no padrão BRL
                const valorFormatado = parseFloat(item.valor_total)
                    .toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                // ✅ Converte datas para formato BR
                const dataCompra = formatarDataBR(item.data_compra);
                const dataEntrada = formatarDataBR(item.data_entrada);

                return `
                    <tr>
                        <td data-label="Fornecedor">${item.fornecedor_razao}</td>
                        <td data-label="Data da Compra">${dataCompra}</td>
                        <td data-label="Data da Entrada">${dataEntrada}</td>
                        <td data-label="Valor Total">${valorFormatado}</td>
                    </tr>
                `;
            }).join('');

            tbody.html(rows);
        },
        error: function(xhr, status, error) {
            tbody.html(`<tr><td colspan="4" class="text-center text-danger">Erro ao carregar dados: ${error}</td></tr>`);
        }
    });

    const modal = new bootstrap.Modal(document.getElementById('modalFornecedores'));
    modal.show();
});

/**
 * 🔹 Função auxiliar para converter data do formato YYYY-MM-DD → DD/MM/YYYY
 */
function formatarDataBR(dataISO) {
    if (!dataISO) return '-';
    const partes = dataISO.split('-');
    if (partes.length !== 3) return dataISO;
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}
