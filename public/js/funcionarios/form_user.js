document.addEventListener("DOMContentLoaded", function () {
  const fields = [
    "user_name",

    "empresa_id",
    "grupo_id",
    "setor_id",
    "user_function_id",
    "user_status",

  ];

  const btn = document.getElementById("editBtn") || document.getElementById("submitBtn");
  const userId = document.getElementById("user_id");

function validateField(el) {
  if (!el) return true;

  const id = el.id;
  const value = el.value?.trim() ?? "";
  const userFunction = parseInt(document.getElementById("user_function_id")?.value || 0);
  let valid = true;

  const isFunc8 = userFunction === 8;

  // Nome obrigatório sempre
  if (id === "user_name") valid = value !== "";

  // Email obrigatório SOMENTE se user_function_id != 8
  if (id === "user_email") {
    if (isFunc8) {
      valid = true; // opcional
    } else {
      valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }
  }

  // Campos de select (sempre obrigatórios)
  if (["empresa_id", "grupo_id", "setor_id", "user_function_id", "user_status"].includes(id)) {
    valid = value !== "" && value !== null && value !== "Carregando...";
  }

  // Senha:
  // - Se func != 8 → continua obrigatório
  // - Se func == 8 → opcional (mas se preencher, mínimo 6)
  if (id === "user_pass") {
    if (isFunc8) {
      valid = value.length === 0 || value.length >= 6;
    } else {
      if (!userId?.value) {
        valid = value.length >= 6; // novo cadastro
      } else {
        valid = value.length === 0 || value.length >= 6; // edição
      }
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
