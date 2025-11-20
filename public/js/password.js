window.addEventListener('DOMContentLoaded', () => {
    const recoverBtn = document.getElementById('recoverBtn');
    recoverBtn.disabled = true;

    const pass1 = document.getElementById('pass1');
    const pass2 = document.getElementById('pass2');

    function checkPasswords() {
        if (pass1.value !== '' && pass1.value === pass2.value) {
            recoverBtn.disabled = false;
            pass2.classList.remove('is-invalid'); // senhas iguais, remove erro
        } else {
            recoverBtn.disabled = true;
            // só adiciona a classe de erro se o pass2 estiver focado
            if (document.activeElement === pass2) {
                pass2.classList.add('is-invalid');
            } else {
                pass2.classList.remove('is-invalid');
            }
        }
    }

    pass1.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);
});
