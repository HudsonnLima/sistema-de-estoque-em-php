// ------------------------------------------------------------
// Pegando elementos com segurança
// ------------------------------------------------------------
const agruparCheckbox = document.getElementById('agrupar');
const operacaoSelect = document.getElementById('operacao');
const pesquisaLabel = document.getElementById('pesquisaLabel');
const pesquisaInput = document.getElementById('pesquisaInput');
const searchForm = document.getElementById('searchForm');
const limparCampos = document.getElementById('limparCampos');


// ------------------------------------------------------------
// Função: Ativa/Desativa campo de pesquisa se houver checkbox
// ------------------------------------------------------------
function toggleInputs() {
    const pesquisaInput = document.getElementById('pesquisaInput');
    if (!pesquisaInput) return;

    if (agruparCheckbox && agruparCheckbox.checked) {
        pesquisaInput.value = '';
        pesquisaInput.disabled = true;
    } else {
        pesquisaInput.disabled = false;
    }
}


// ------------------------------------------------------------
// Checkbox "agrupar" — PROTEGIDO
// ------------------------------------------------------------
if (agruparCheckbox) {
    agruparCheckbox.addEventListener('change', toggleInputs);
    window.addEventListener('load', toggleInputs);
}


// ------------------------------------------------------------
// Atualiza label e name do campo dinamicamente
// ------------------------------------------------------------
function atualizarLabelEName() {
    if (!operacaoSelect || !pesquisaLabel || !pesquisaInput) return;

    if (operacaoSelect.value === "0") {
        pesquisaLabel.textContent = "Pesquisar por funcionário";
        pesquisaInput.name = "funcionario";
    } else if (operacaoSelect.value === "1") {
        pesquisaLabel.textContent = "Pesquisar por fornecedor";
        pesquisaInput.name = "fornecedor";
    }
}

document.addEventListener('DOMContentLoaded', atualizarLabelEName);

if (operacaoSelect) {
    operacaoSelect.addEventListener('change', atualizarLabelEName);
}


// ------------------------------------------------------------
// Formulário de busca — PROTEGIDO
// ------------------------------------------------------------
if (searchForm) {
    searchForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const produto = document.getElementById('produto');
        const setor = document.getElementById('setor_id');
        const pesquisaInput = document.getElementById('pesquisaInput');
        const data_inicio = document.getElementById('data_inicio');
        const data_final = document.getElementById('data_final');

        const baseUri = `${BASE_URL}/relatorios`;

        // Remove atributos name se valores vazios
        if (produto && !produto.value.trim()) produto.removeAttribute('name');
        if (setor && !setor.value.trim()) setor.removeAttribute('name');
        if (pesquisaInput && !pesquisaInput.value.trim()) pesquisaInput.removeAttribute('name');
        if (data_inicio && !data_inicio.value.trim()) data_inicio.removeAttribute('name');
        if (data_final && !data_final.value.trim()) data_final.removeAttribute('name');

        // Monta URL
        const formData = new URLSearchParams(new FormData(searchForm)).toString();
        const newUrl = `${baseUri}?${formData}`;

        window.location.href = newUrl;
    });
}


// ------------------------------------------------------------
// Botão - Limpar Campos — PROTEGIDO
// ------------------------------------------------------------
if (limparCampos) {
    const currentUrl = new URL(window.location.href);
    const baseUri = currentUrl.origin + currentUrl.pathname;

    limparCampos.addEventListener('click', function() {

        const setor = document.getElementById('setor_id');
        const produto = document.getElementById('produto');
        const pesquisaInput = document.getElementById('pesquisaInput');
        const data_inicio = document.getElementById('data_inicio');
        const data_final = document.getElementById('data_final');

        if (setor) setor.value = '';
        if (produto) produto.value = '';
        if (pesquisaInput) pesquisaInput.value = '';
        if (data_inicio) data_inicio.value = '';
        if (data_final) data_final.value = '';

        window.history.pushState({ path: baseUri }, '', baseUri);

        window.location.href = baseUri;
    });
}
