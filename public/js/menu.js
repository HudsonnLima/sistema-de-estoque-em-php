(function() {
  // Função global para alternar submenu via HTML onclick
  window.toggleSubmenu = function(id, el) {
      const submenu = document.getElementById(id);
      submenu.classList.toggle('show');

      const icon = el.querySelector('i');
      if (submenu.classList.contains('show')) {
          icon.classList.remove('fa-angles-down');
          icon.classList.add('fa-angles-up');
      } else {
          icon.classList.remove('fa-angles-up');
          icon.classList.add('fa-angles-down');
      }
  };

  // Recolher o menu automaticamente ao carregar a página em telas pequenas
  document.addEventListener("DOMContentLoaded", function () {
      const sidebar = document.getElementById("sidebar");

      if (!sidebar) return;

      if (window.innerWidth <= 767.98) {
          sidebar.classList.add("collapsed");
          document.body.classList.remove("sidebar-open");
      }
  });

  // Botão de toggle do menu
  const toggleButton = document.getElementById('toggleSidebar');
  if (toggleButton) {
      toggleButton.addEventListener('click', () => {
          if (window.innerWidth <= 767.98) {
              // Mantém a classe collapsed sempre no mobile
              document.body.classList.toggle('sidebar-open');
          } else {
              // Em telas maiores, colapsa normalmente
              const sidebar = document.getElementById('sidebar');
              if (!sidebar) return;
              sidebar.classList.toggle('collapsed');
          }
      });
  }
})();
