const modal = document.getElementById('modalEditarCompra');

modal.addEventListener('shown.bs.modal', function () {
  const dataCompra = document.getElementById('data');
  const dataEntrega = document.getElementById('data_entrega');
  const submitBtn = document.getElementById('submitBtnModal'); // <---- ATENÇÃO AQUI

  if (!dataCompra || !dataEntrega || !submitBtn) return;

  // limpa classes ao abrir
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
