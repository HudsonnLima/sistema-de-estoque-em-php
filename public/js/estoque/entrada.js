console.log("estoque/entrada.js carregado!");
//BLOQUEIA O SUBMIT SE A DATA FOR MAIOR QUE A DATA ATUAL
document.addEventListener('DOMContentLoaded', function() {
    const dataInput = document.getElementById('data');
    const submitBtn = document.getElementById('submitBtn');
  
    dataInput.addEventListener('change', function() {
        const selectedDate = new Date(dataInput.value);
        const currentDate = new Date();
  
        if (selectedDate > currentDate) {
            dataInput.style.border = '1px solid red';
            submitBtn.disabled = true;
        } else {
            dataInput.style.border = '';
            submitBtn.disabled = false;
        }
    });
  });
  //MASKMONEY
  $(document).ready(function () {
    const precoInputs = document.querySelectorAll('.preco');
    const submitBtn = document.getElementById('submitBtn');
  
    function validateMoneyInput(input) {
        const value = parseFloat(input.value.replace(/\./g, '').replace(',', '.')); // Converte o valor para float
        if (isNaN(value)) {
            input.classList.remove('error', 'is-invalid', 'is-valid');
            return false;
        }

        /*
        if (value >= 0 && value <= 99000) {
            input.classList.remove('error');
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
            return true;
        } else {
            input.classList.add('error');
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        }
            */
    }
  
    function validateForm() {
        let isValid = true;
        precoInputs.forEach(input => {
            isValid = validateMoneyInput(input) && isValid;
        });
        submitBtn.disabled = !isValid;
    }
  
    precoInputs.forEach(input => {
        input.addEventListener('blur', function () {
            validateMoneyInput(input);
            validateForm();
        });
  
        input.addEventListener('input', function () {
            validateForm();
        });
  
        // Aplicar máscara de moeda ao preco com limite até 99.000,00
        $(input).mask('99.000,00', { reverse: true });
  
        // Limitar a entrada de caracteres no campo de moeda
        input.addEventListener('input', function () {
            const value = input.value.replace(/[^0-9]/g, '');
            if (value.length > 8) { // Permite até 8 caracteres para incluir o valor máximo permitido
                input.value = input.value.substring(0, input.value.length - 1);
            }
  
            // Validação da borda verde/vermelha
            validateMoneyInput(input);
            if (input.classList.contains('is-valid')) {
                input.style.borderColor = 'green';
            } else {
                input.style.borderColor = 'red';
            }
        });
  
        // Formatar valor inicial do campo conforme a máscara
        if (input.value) {
            $(input).val(function (index, value) {
                return parseFloat(value.replace(/\./g, '').replace(',', '.')).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            });
            $(input).trigger('input'); // Dispara evento input para aplicar máscara e validação
        }
    });
});

