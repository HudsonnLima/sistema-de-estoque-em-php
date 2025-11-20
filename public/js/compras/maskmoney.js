$(document).ready(function () {
    aplicarMascaraMoeda(); // Chamar a função para aplicar a máscara nos campos existentes

    // Valida todos os campos com a classe "preco" ao carregar a página
    $('.preco').each(function () {
        if (this.value) {
            validateMoneyInput(this); // Aplica a borda verde se o campo estiver preenchido corretamente
        }
    });
});

// Função para aplicar a máscara de moeda
function aplicarMascaraMoeda() {
    // Aplicar máscara de moeda aos campos com a classe "preco"
    $('.preco').on('input', function () {
        let value = this.value.replace(/[^0-9]/g, '');

        // Remove os zeros à esquerda conforme o usuário digita
        value = value.replace(/^0+/, '');

        // Se o valor for menor que 3 dígitos, adicionar zeros à esquerda
        if (value.length === 1) {
            value = '00' + value;
        } else if (value.length === 2) {
            value = '0' + value;
        }

        // Aplica a máscara manualmente para garantir o formato correto
        value = value.replace(/(\d)(\d{2})$/, '$1,$2'); // Adiciona a vírgula antes dos últimos dois dígitos
        value = value.replace(/(?=(\d{3})+(\D))\B/g, "."); // Adiciona pontos a cada grupo de três dígitos

        this.value = value;

        // Validação da borda verde/vermelha
        validateMoneyInput(this);

        // Atualizar o campo relacionado e validar
        updateRelatedField(this);
    });

    // Adicionar evento de validação ao perder o foco dos campos com a classe "preco"
    $('.preco').on('blur', function () {
        if (!this.value) {
            // Se o campo for deixado vazio após a interação, exibe borda vermelha
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            this.style.borderColor = 'red';
        } else {
            validateMoneyInput(this); // Revalida para garantir a borda verde se houver valor
        }
        validateForm();
    });
}

// Função para atualizar o campo relacionado (preco_un <-> preco_tt)
function updateRelatedField(input) {
    const inputId = input.id;
    let relatedInput;

    // Identificar o campo relacionado com base no id atual
    if (inputId.includes('preco_un')) {
        relatedInput = document.querySelector(`#preco_tt_${inputId.split('_')[2]}`);
    } else if (inputId.includes('preco_tt')) {
        relatedInput = document.querySelector(`#preco_un_${inputId.split('_')[2]}`);
    }

    // Atualizar e validar o campo relacionado
    if (relatedInput) {
        let valorAtual = parseFloat(input.value.replace(/\./g, '').replace(',', '.')) || 0;
        const quantity = 1; // Aqui você deve pegar a quantidade correspondente

        let novoValor = 0;

        if (inputId.includes('preco_un')) {
            // Atualizar o preço total se o preço unitário for preenchido
            novoValor = valorAtual * quantity;
        } else if (inputId.includes('preco_tt')) {
            // Atualizar o preço unitário se o preço total for preenchido
            novoValor = valorAtual / quantity;
        }

        if (!isNaN(novoValor) && novoValor > 0) {
            relatedInput.value = formatarMoeda(novoValor);
            relatedInput.classList.add('is-valid');
            relatedInput.classList.remove('is-invalid');
            relatedInput.style.borderColor = 'green';
        } else {
            relatedInput.value = '';
            relatedInput.classList.add('is-invalid');
            relatedInput.classList.remove('is-valid');
            relatedInput.style.borderColor = 'red';
        }

        validateForm(); // Revalida o formulário após a atualização do campo relacionado
    }
}


// Função de validação de campos monetários
function validateMoneyInput(input) {
    const value = parseFloat(input.value.replace(/\./g, '').replace(',', '.'));
    if (value >= 0 && value <= 99000) {
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
        input.style.borderColor = 'green';
    } else {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        input.style.borderColor = 'red';
    }
}

// Função de validação do formulário
function validateForm() {
    const invalidInputs = document.querySelectorAll('.is-invalid');
    const submitBtn = document.querySelector('#submitBtn'); // 🔹 Corrigido seletor

    if (!submitBtn) return; // Evita erro caso o botão ainda não exista

    submitBtn.disabled = invalidInputs.length > 0;
}

function calcularTotal() {
    let totalCompra = 0;

    document.querySelectorAll('.produto').forEach(function (row) {
        let quantidade = parseFloat(row.querySelector('.quantidade').value.replace(/\./g, '').replace(',', '.')) || 0;
        let precoInput = row.querySelector('.preco');
        let totalInput = row.querySelector('.total');
        let preco = parseFloat(precoInput.value.replace(/\./g, '').replace(',', '.')) || 0;
        let total = parseFloat(totalInput.value.replace(/\./g, '').replace(',', '.')) || 0;

        if (quantidade > 0) {
            if (preco > 0 && totalInput !== document.activeElement) {
                // Calcula o total se o preço foi digitado e o campo total não está em foco
                total = quantidade * preco;
                totalInput.value = formatarMoeda(total);
            } else if (total > 0 && precoInput !== document.activeElement) {
                // Calcula o preço se o total foi digitado e o campo preço não está em foco
                preco = total / quantidade;
                precoInput.value = formatarMoeda(preco);
            }
        }

        totalCompra += total;
    });

    // Atualiza o valor total da compra
    $('#totalCompra').text(formatarMoeda(totalCompra));
}

// Função para formatar o valor monetário
function formatarMoeda(valor) {
    return valor.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
}
