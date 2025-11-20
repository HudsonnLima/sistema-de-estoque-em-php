document.addEventListener("DOMContentLoaded", function () {
    const form = document.forms["fornecedorForm"];
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

    

    validarCampos();
});
