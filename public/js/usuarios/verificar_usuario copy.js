document.addEventListener('DOMContentLoaded', () => {
  const campos = ['user_name', 'user_email'];
  const submitBtn = document.getElementById('editBtn') || document.getElementById('submitBtn');
  const currentUserId = document.getElementById('user_id')?.value || '';

  const validarEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

  campos.forEach(campo => {
    const input = document.getElementById(campo);
    if (!input) return;

    input.addEventListener('blur', () => {
      const valor = (input.value || '').trim();
      if (valor === '') return;

      // Se for e-mail e formato inválido → marca como inválido
      if (campo === 'user_email' && !validarEmail(valor)) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if (submitBtn) submitBtn.disabled = true;
        return;
      }

      // 🔹 Em edição, se o campo não mudou, NÃO faz verificação no servidor
      if (currentUserId && input.dataset.original === valor) {
        input.classList.remove('is-invalid');
        //input.classList.add('is-valid');
        return;
      }

      fetch(`${API_URL}/usuarios/verificar_usuario.php`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({campo, valor, user_id: currentUserId})
      })
      .then(resp => resp.text())
      .then(text => {
        let data;
        try {
          data = JSON.parse(text);
        } catch (err) {
          console.error('Resposta inválida de verificar_usuario.php:', text);
          input.classList.add('is-invalid');
          //input.classList.remove('is-valid');
          if (submitBtn) submitBtn.disabled = true;
          return;
        }

        // Se já existe e não é o mesmo user_id, inválido
        if (data.existe) {
          input.classList.add('is-invalid');
          //input.classList.remove('is-valid');
          if (submitBtn) submitBtn.disabled = true;
        } else {
          //input.classList.add('is-valid');
          input.classList.remove('is-invalid');
        }
      })
      .catch(err => {
        console.error(err);
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if (submitBtn) submitBtn.disabled = true;
      });
    });

    // Guarda o valor original do campo (para saber se foi alterado)
    input.dataset.original = input.value;
  });
});
