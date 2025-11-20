document.addEventListener('DOMContentLoaded', () => {
    const campos = ['user_name', 'user_email'];
    const submitBtn = document.getElementById('submitBtn') || document.getElementById('editBtn');
    const currentUserId = document.getElementById('user_id')?.value || null;

    const validarEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    campos.forEach(campo => {
        const input = document.getElementById(campo);

        input.addEventListener('blur', () => {
            const valor = input.value.trim();
            if (valor === '') return;

            if (campo === 'user_email' && !validarEmail(valor)) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                if (submitBtn) submitBtn.disabled = true; // desabilita
                return;
            }

            fetch(`${API_URL}/usuarios/verificar_usuario.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ campo, valor, user_id: currentUserId })
            })
                .then(resp => resp.json())
                .then(data => {
                    if (data.existe) {
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');
                    } else {
                        input.classList.add('is-valid');
                        input.classList.remove('is-invalid');
                    }

                    // Atualiza o botão conforme o estado atual de todos os campos
                    if (window.validateAll) window.validateAll();
                })
                .catch(err => console.error(err));
        });
    });
});
