// excluirUsuario.js - enviando deleteBtn + user_id para index.php (Controller handles it)
document.addEventListener("DOMContentLoaded", function() {
  let usuarioIdToDelete = null;
  const modalEl = document.getElementById('modalExcluirUsuario');

  if (!modalEl) {
    console.error('Modal #modalExcluirUsuario não encontrado.');
    return;
  }

  const excluirModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });

  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.excluirUsuario');
    if (!btn) return;

    e.preventDefault();
    usuarioIdToDelete = btn.dataset.id || null;

    // pega nome da linha (primeira coluna)
    const row = btn.closest('tr');
    const userName = (row ? row.querySelector('td') : null);
    document.getElementById('modalDetalheUsuario').innerHTML = `<p><strong>Usuário:</strong> ${userName ? userName.textContent.trim() : '—'}</p>`;

    excluirModal.show();
  });

  document.getElementById('confirmarExclusao').addEventListener('click', function(e) {
    e.preventDefault();
    if (!usuarioIdToDelete) return;

    const fd = new FormData();
    fd.append('deleteBtn', 1);
    fd.append('user_id', usuarioIdToDelete);

    fetch('index.php', {
      method: 'POST',
      body: fd,
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(json => {
      // Ao receber JSON do controller, agora sim recarregamos a página para exibir o flash
      // (Controller já setou $_SESSION)
      window.location.reload();
    })
    .catch(err => {
      // Mesmo em erro, tentar reload para que a sessão (se setada) seja exibida
      console.error('Erro ao excluir:', err);
      window.location.reload();
    });
  });

  // limpa estado ao fechar modal
  modalEl.addEventListener('hidden.bs.modal', function () {
    usuarioIdToDelete = null;
    document.getElementById('modalDetalheUsuario').innerHTML = '';
  });
});
