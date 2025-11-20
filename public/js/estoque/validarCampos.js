document.addEventListener("DOMContentLoaded", function () {
    const form = document.forms["cadProdutos"];
    const submitBtn = document.getElementById("submitBtn");

    submitBtn.disabled = true;

    // todos os required do form
    const inputs = form.querySelectorAll("input[required], select[required]");

    function validarCampos() {
        const todosPreenchidos = Array.from(inputs)
            .every(input => input.value.trim() !== "");
        submitBtn.disabled = !todosPreenchidos;
    }

    // monitora mudança nos required
    inputs.forEach(input => {
        input.addEventListener("input", validarCampos);
        input.addEventListener("change", validarCampos);
    });

    // validação numérica só nos campos com IDs especificados
    const estoqueMin = document.getElementById("estoque_minimo");
    const estoqueMax = document.getElementById("estoque_maximo");

    [estoqueMin, estoqueMax].forEach(campo => {
        campo.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, ""); // permite só números
            validarCampos();
        });
    });

    validarCampos();
});
