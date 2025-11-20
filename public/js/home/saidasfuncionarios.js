console.log("public/js/home/saidasfuncionarios.js (home) carregado!");
document.addEventListener('DOMContentLoaded', () => {
  fetch(`${API_URL}/home/saidasfuncionarios.php`)
    .then(res => res.json())
    .then(dados => {
      const ctx = document.getElementById('saidaProdutos').getContext('2d');

      // Cores aleatórias sem repetir
      const cores = dados.labels.map(() => 
        `hsl(${Math.floor(Math.random() * 360)}, 70%, 50%)`
      );

      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: dados.labels,
          datasets: [{
            data: dados.valores,
            backgroundColor: cores
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return `${context.label}: ${context.raw}`;
                }
              }
            }
          }
        }
      });
    })
    .catch(err => console.error('Erro ao carregar gráfico:', err));
});
