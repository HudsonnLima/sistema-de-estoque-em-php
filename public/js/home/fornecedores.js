console.log("public/js/home/fornecedores.js (home) carregado!");
document.addEventListener('DOMContentLoaded', () => {
  fetch(`${API_URL}/home/fornecedores.php`)
    .then(res => res.json())
    .then(response => {
      if (response.success) {
        const labels = response.data.map(item => item.fornecedor_fantasia);
        const data = response.data.map(item => parseFloat(item.total_compras));
        const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];

        new Chart(document.getElementById('fornecedores'), {
          type: 'bar',
          data: {
            labels: labels, // necessário para associar dados, mesmo sem mostrar no eixo
            datasets: [{
              label: 'Total Comprado',
              data: data,
              backgroundColor: colors
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: true,
                position: 'bottom',
                labels: {
                  generateLabels: chart => {
                    return chart.data.labels.map((label, i) => ({
                      text: label,
                      fillStyle: colors[i],
                      strokeStyle: colors[i],
                      index: i
                    }));
                  }
                }
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return 'R$ ' + context.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                  }
                }
              }
            },
            scales: {
              x: {
                ticks: {
                  display: false // oculta os nomes no eixo X
                },
                grid: {
                  display: false // opcional: remove linhas verticais
                }
              }
            }
          }
        });

      } else {
        console.error('Erro na API:', response.message);
      }
    })
    .catch(err => console.error(err));
});
