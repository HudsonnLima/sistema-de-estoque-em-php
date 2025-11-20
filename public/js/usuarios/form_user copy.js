document.addEventListener("DOMContentLoaded", function () {
  const fields = [
    "user_name",
    "user_email",
    "empresa_id",
    "grupo_id",
    "setor_id",
    "user_function_id",
    "user_status",
    "user_pass"
  ];

  const btn = document.getElementById("editBtn") || document.getElementById("submitBtn");
  const userId = document.getElementById("user_id");

  function validateField(el) {
    if (!el) return true;
    const id = el.id;
    const value = el.value?.trim() ?? "";
    let valid = true;

    if (id === "user_name") valid = value !== "";
    if (id === "user_email") valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    if (["empresa_id", "grupo_id", "setor_id", "user_function_id", "user_status"].includes(id))
      valid = value !== "" && value !== null && value !== "Carregando...";

    // Senha obrigatória só se for novo cadastro
    if (id === "user_pass") {
      if (!userId?.value) {
        valid = value.length >= 6;
      } else {
        valid = value.length === 0 || value.length >= 6;
      }
    }

    return valid;
  }

  function validateAll() {
    let allValid = true;

    fields.forEach(id => {
      const el = document.getElementById(id);
      if (!el || !validateField(el)) allValid = false;
      // ⚠️ Checa se o campo já está marcado como inválido pelo verificar_usuario.js
      if (el && el.classList.contains('is-invalid')) allValid = false;
    });

    if (btn) btn.disabled = !allValid;
  }

  // eventos de input e change
  fields.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener("input", validateAll);
    el.addEventListener("change", validateAll);
  });

  // 🔹 Se o setor for preenchido dinamicamente depois
  const setorSelect = document.getElementById("setor_id");
  if (setorSelect) {
    const observer = new MutationObserver(() => validateAll());
    observer.observe(setorSelect, { childList: true, subtree: true });
  }

  // validação inicial
  setTimeout(validateAll, 500); // pequeno atraso para aguardar carregamento dinâmico
  window.validateAll = validateAll;

});
