document.getElementById('pass2').addEventListener('input', function() {
    const pass1 = document.getElementById('pass1').value;
    const pass2 = document.getElementById('pass2').value;
    const recoverpass = document.getElementById('recoverpass');

    if (pass1 !== pass2) {
        this.style.border = '2px solid red';
        recoverpass.disabled = true;
    } else {
        this.style.border = '';
        recoverpass.disabled = false;
    }
});

document.getElementById('pass1').addEventListener('input', function() {
    const pass1 = document.getElementById('pass1').value;
    const pass2 = document.getElementById('pass2').value;
    const pass2Input = document.getElementById('pass2');
    const recoverpass = document.getElementById('recoverpass');

    if (pass2 !== '' && pass1 !== pass2) {
        pass2Input.style.border = '2px solid red';
        recoverpass.disabled = true;
    } else {
        pass2Input.style.border = '';
        recoverpass.disabled = false;
    }
});