console.log("checkdate.js (estoque) carregado!");

//BLOQUEIA O SUBMIT SE A DATA FOR MAIOR QUE A DATA ATUAL
document.addEventListener('DOMContentLoaded', function () {
    const dataInput = document.getElementById('data');
    const submitBtn = document.getElementById('submitBtn');

    dataInput.addEventListener('change', function () {
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

//BLOQUEIA O SUBMIT SE A DATA FOR MENOR QUE A DATA ATUAL
/*
document.addEventListener('DOMContentLoaded', function () {
    const dataInput = document.getElementById('data_entrega');
    const submitBtn = document.getElementById('submitBtn');

    dataInput.addEventListener('change', function () {
        const selectedDate = new Date(dataInput.value);
        const currentDate = new Date();
        currentDate.setHours(0, 0, 0, 0); // Para garantir que a comparação seja feita apenas com a data, sem considerar horas
        selectedDate.setHours(0, 0, 0, 0); // Garantir que a comparação seja feita apenas com a data, sem considerar horas

        if (selectedDate < currentDate) {
            dataInput.style.border = '1px solid red';
            submitBtn.disabled = true;
        } else {
            dataInput.style.border = '';
            submitBtn.disabled = false;
        }
    });
});
*/