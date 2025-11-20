document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalEditarCompra');

    if (!modal) return; // evita erros se o modal não existir

    modal.addEventListener('shown.bs.modal', function () {
        const dataCompra = document.getElementById('data');
        const dataEntrega = document.getElementById('data_entrega');
        const submitBtn = document.getElementById('submitBtnModal');

        if (!dataCompra || !dataEntrega || !submitBtn) return;

        dataEntrega.classList.remove('is-invalid', 'is-valid');
        submitBtn.disabled = false;

        dataEntrega.addEventListener('change', function () {
            const compraDate = new Date(dataCompra.value + 'T00:00:00');
            const entregaDate = new Date(dataEntrega.value + 'T00:00:00');

            if (entregaDate < compraDate) {
                dataEntrega.classList.add('is-invalid');
                submitBtn.disabled = true;
            } else {
                dataEntrega.classList.remove('is-invalid');
                submitBtn.disabled = false;
            }
        });
    });
});
