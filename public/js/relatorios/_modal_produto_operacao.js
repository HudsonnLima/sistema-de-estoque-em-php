// _modal_produto_operacao.js

// -----------------------------
// Helpers
// -----------------------------
function formatarDataBR(dataISO) {
    if (!dataISO) return '-';
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(dataISO)) return dataISO;
    const match = String(dataISO).match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!match) return dataISO;
    const [, ano, mes, dia] = match;
    return `${dia}/${mes}/${ano}`;
}

function escapeHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// -----------------------------
// Handler de clique
// -----------------------------
$(document).on('click', '.abrir-modal-produto', function (e) {
    e.preventDefault();

    const produto_id = $(this).data('id');
    const produto_nome = $(this).data('nome') || 'Produto';
    const operacao = Number($(this).data('operacao') || 0); // 0 = saídas, 1 = entradas

    $('#modalProdutoTitle').text(produto_nome);

    const thead = $('#modalProdutoHead');
    const tbody = $('#modalProdutoBody');

    const colspan = operacao === 1 ? 5 : 4;

    tbody.html(`<tr><td colspan="${colspan}" class="text-center">Carregando...</td></tr>`);

    // -----------------------------
    // MONTA O THEAD
    // -----------------------------
    if (operacao === 0) {
        thead.html(`
            <tr>
                <th class="text-start">Produto</th>
                <th class="text-start">Funcionário</th>
                <th>Data</th>
                <th>Quantidade</th>
            </tr>
        `);
    } else {
        thead.html(`
            <tr>
                <th class="text-start">Produto</th>
                <th class="text-start">Fornecedor</th>
                <th>Data</th>
                <th>Quantidade</th>
                <th>Preço</th>
            </tr>
        `);
    }

    // -----------------------------
    // 🔥 FORÇA O THEAD A SUMIR NO MOBILE (sem alterar CSS)
    // -----------------------------
    $('#modalProduto table thead').hide();

    // -----------------------------
    // AJAX
    // -----------------------------
    $.ajax({
        url: `${API_URL}/relatorios/_modal_produto_operacao.php`,
        method: 'GET',
        data: { produto_id, operacao },
        dataType: 'json',
        success: function (data) {

            if (data && data.error) {
                tbody.html(`<tr><td colspan="${colspan}" class="text-center text-danger">${escapeHtml(data.error)}</td></tr>`);
                return;
            }

            if (!Array.isArray(data) || data.length === 0) {
                tbody.html(`<tr><td colspan="${colspan}" class="text-center">Nenhum registro encontrado</td></tr>`);
                return;
            }

            let rows = '';

            if (operacao === 0) {
                rows = data.map(item => `
                    <tr>
                        <td data-label="Produto">${escapeHtml(item.produto_nome)}</td>
                        <td data-label="Funcionário">${escapeHtml(item.funcionario_nome || '-')}</td>
                        <td data-label="Data">${formatarDataBR(item.cad_data)}</td>
                        <td data-label="Quantidade">${item.quantidade ?? '-'}</td>
                    </tr>`).join('');
            } else {
                rows = data.map(item => {
                    const preco = (item.preco !== null && item.preco !== '' && item.preco !== undefined)
                        ? Number(item.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
                        : '-';

                    return `
                        <tr>
                            <td data-label="Produto">${escapeHtml(item.produto_nome)}</td>
                            <td data-label="Fornecedor">${escapeHtml(item.fornecedor_nome || '-')}</td>
                            <td data-label="Data">${formatarDataBR(item.cad_data)}</td>
                            <td data-label="Quantidade">${item.quantidade ?? '-'}</td>
                            <td data-label="Preço">${preco}</td>
                        </tr>`;
                }).join('');
            }

            tbody.html(rows);

            // Esconde novamente caso o mobile re-renderize
            $('#modalProduto table thead').hide();
        },

        error: function (xhr, status, error) {
            tbody.html(`<tr><td colspan="${colspan}" class="text-center text-danger">Erro: ${escapeHtml(error || status)}</td></tr>`);
        }
    });

    // -----------------------------
    // Exibe o modal
    // -----------------------------
    const modalEl = document.getElementById('modalProduto');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
});
