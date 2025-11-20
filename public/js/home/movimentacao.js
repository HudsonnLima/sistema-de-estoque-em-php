console.log("public/js/home/movimentacao.js (home) carregado!");
document.addEventListener('DOMContentLoaded', () => {
  fetch(`${API_URL}/home/movimentacao.php`)
    .then(res => res.json())
    .then(dados => {
      const ctx = document.getElementById('movimentacao').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: dados.labels,
          datasets: [{
            label: 'Entradas',
            data: dados.entradas,
            backgroundColor: '#0d6efd'
          }, {
            label: 'Saídas',
            data: dados.saidas,
            backgroundColor: '#dc3545'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          },
          plugins: {
            legend: {
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return `${context.dataset.label}: ${context.parsed.y}`;
                }
              }
            }
          }
        }
      });
    })
    .catch(err => console.error('Erro ao carregar gráfico:', err));
});

