<?php
if ($_SESSION['user_function_id'] == 8) {
  header('Location: ' . BASE_URL . '/chamados');
  exit;
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  canvas {
    max-height: 220px !important;
    /* ajuste aqui */
  }
</style>
<h2 class="mb-4 text-left">📊 Dashboard de Chamados</h2>

<!-- Ajustado: row g-3 -->
<div class="row row-cols-1 row-cols-md-2 g-3">

<div class="col">
  <div class="card shadow rounded-3 h-100">
    <div class="card-body">
      <h5 class="card-title">Entradas x Saídas (Últimos 6 Meses)</h5>
      <canvas id="movimentacao"></canvas>
    </div>
  </div>
</div>

  <!-- Exemplo de gráfico -->
  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Chamados por Status</h5>
        <canvas id="chamadosStatus"></canvas>
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Chamados por Prioridade</h5>
        <canvas id="chamadosPrioridade"></canvas>
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Produtos x Saídas</h5> 
        <canvas id="saidaProdutos"></canvas> 
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Chamados nos Últimos Meses</h5>
        <canvas id="chamadosMeses"></canvas>
      </div>
    </div>
  </div>



  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Tempo Médio de Resolução (Horas)</h5>
        <canvas id="tempoResolucao"></canvas>
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Chamados por Categoria</h5>
        <canvas id="chamadosCategoria"></canvas>
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">% de Resolução no Prazo</h5>
        <canvas id="resolucaoPrazo"></canvas>
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Chamados por Hora do Dia</h5>
        <canvas id="chamadosHora"></canvas>
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Abertos x Fechados (Últimos Meses)</h5>
        <canvas id="abertosFechados"></canvas>
      </div>
    </div>
  </div>

</div>

<?php
//QUANTIDADE DE CHAMADOS POR STATUS

$statusStmt = $pdo->prepare("SELECT chamado_status, COUNT(*) as total 
FROM chamados GROUP BY chamado_status");


//QUANTIDADE DE CHAMADOS POR STATUS NO MÊS ATUAL
/*
$statusStmt = $pdo->prepare(
    "SELECT chamado_status, COUNT(*) as total
     FROM chamados
     WHERE YEAR(chamado_abertura) = YEAR(CURDATE())
       AND MONTH(chamado_abertura) = MONTH(CURDATE())
     GROUP BY chamado_status"
);
*/

$statusStmt->execute();
$resultados = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

$status = [
  0 => 0, // Aberto
  1 => 0, // Andamento
  2 => 0  // Fechado
];
foreach ($resultados as $row) {
  $status[$row['chamado_status']] = $row['total'];
}

//QUANTIDADE DE CHAMADOS POR SETOR
/*
$sql = "SELECT s.setor_nome, COUNT(c.chamado_id) AS total
        FROM chamados c
        JOIN setor s ON c.chamado_setor_id = s.setor_id
        WHERE MONTH(c.chamado_abertura) = MONTH(CURDATE())
          AND YEAR(c.chamado_abertura) = YEAR(CURDATE())
        GROUP BY s.setor_nome";*/

$sql = "SELECT s.setor_nome, COUNT(c.chamado_id) AS total
        FROM chamados c
        JOIN setor s ON c.chamado_setor_id = s.setor_id
        GROUP BY s.setor_nome";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Montar arrays de labels e valores
$labels = [];
$valores = [];

foreach ($resultados as $row) {
  $labels[] = $row['setor_nome'];
  $valores[] = (int)$row['total'];
}

//QUANTIDADE POR CATEGORIA
$sqlCategoria = "		SELECT i.item_nome, COUNT(c.chamado_id) AS total
        FROM chamados c
        JOIN itens i ON c.chamado_item = i.item_id
        WHERE MONTH(c.chamado_abertura) = MONTH(CURDATE())
        AND YEAR(c.chamado_abertura) = YEAR(CURDATE())
        GROUP BY i.item_nome, i.item_id";
$stmtCategoria = $pdo->prepare($sqlCategoria);
$stmtCategoria->execute();
$resultadosCategoria = $stmtCategoria->fetchAll(PDO::FETCH_ASSOC);
// Montar arrays de labels e valores para categorias
$labelsCategoria = [];
$valoresCategoria = [];
foreach ($resultadosCategoria as $row) {
  $labelsCategoria[] = $row['item_nome'];
  $valoresCategoria[] = (int)$row['total'];
}

//QUANTIDADE DE CHAMADOS POR HORA
$sqlHora = "
    SELECT HOUR(chamado_abertura) AS hora, COUNT(*) AS total
    FROM chamados
    WHERE MONTH(chamado_abertura) = MONTH(CURDATE())
      AND YEAR(chamado_abertura) = YEAR(CURDATE())
    GROUP BY HOUR(chamado_abertura)
    ORDER BY hora
";
$stmtHora = $pdo->prepare($sqlHora);
$stmtHora->execute();
$resultadosHora = $stmtHora->fetchAll(PDO::FETCH_ASSOC);

// Montar arrays de labels e valores
$labelsHora = [];
$valoresHora = [];
foreach ($resultadosHora as $row) {
  $labelsHora[] = $row['hora'] . 'h';
  $valoresHora[] = (int)$row['total'];
}

?>


<script>
  // Exemplo de dados para os gráficos
  new Chart(document.getElementById('chamadosStatus'), {
    type: 'doughnut',
    data: {
      labels: ['Abertos', 'Em andamento', 'Finalizados'],
      datasets: [{
        data: [
          <?php echo $status[0]; ?>,
          <?php echo $status[1]; ?>,
          <?php echo $status[2]; ?>
        ],
        backgroundColor: ['#dc3545', '#0d6efd', '#28a745']
      }]
    }
  });
</script>


<script>
  new Chart(document.getElementById('chamadosPrioridade'), {
    type: 'bar',
    data: {
      labels: ['Baixa', 'Média', 'Alta', 'Crítica'],
      datasets: [{
        label: 'Qtd',
        data: [5, 14, 9, 3],
        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
      }]
    }
  });
</script>


<script>
  const labelsSetor = <?php echo json_encode($labels); ?>;
  const valoresSetor = <?php echo json_encode($valores); ?>;

  // Gerar cores aleatórias sem repetir
  const cores = labelsSetor.map(() =>
    `hsl(${Math.floor(Math.random() * 360)}, 70%, 50%)`
  );

  new Chart(document.getElementById('chamadosSetor'), {
    type: 'pie',
    data: {
      labels: labelsSetor,
      datasets: [{
        data: valoresSetor,
        backgroundColor: cores
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top'
        }
      }
    }
  });
</script>


<script>
  new Chart(document.getElementById('chamadosMeses'), {
    type: 'line',
    data: {
      labels: ['Abr', 'Mai', 'Jun', 'Jul', 'Ago'],
      datasets: [{
        label: 'Chamados',
        data: [15, 20, 18, 25, 22],
        borderColor: '#0d6efd',
        fill: true,
        tension: 0.3,
        backgroundColor: 'rgba(13,110,253,0.1)'
      }]
    }
  });
</script>


<script>
  new Chart(document.getElementById('movimentacao'), {
    type: 'bar',
    data: {
      labels: ['Carlos', 'Ana', 'João', 'Mariana'],
      datasets: [{
        label: 'Atribuídos',
        data: [10, 12, 8, 14],
        backgroundColor: '#0d6efd'
      }, {
        label: 'Resolvidos',
        data: [8, 11, 6, 12],
        backgroundColor: '#28a745'
      }]
    }
  });
</script>


<script>
  new Chart(document.getElementById('tempoResolucao'), {
    type: 'line',
    data: {
      labels: ['Abr', 'Mai', 'Jun', 'Jul', 'Ago'],
      datasets: [{
        label: 'Horas',
        data: [6, 5.5, 7, 6.8, 5],
        borderColor: '#fd7e14',
        fill: false,
        tension: 0.3
      }]
    }
  });
</script>


<script>
  const labelsCategoria = <?php echo json_encode($labelsCategoria); ?>;
  const valoresCategoria = <?php echo json_encode($valoresCategoria); ?>;

  // Gerar cores aleatórias sem repetir
  const coresCategoria = labelsCategoria.map(() =>
    `hsl(${Math.floor(Math.random() * 360)}, 70%, 50%)`
  );

  new Chart(document.getElementById('chamadosCategoria'), {
    type: 'polarArea',
    data: {
      labels: labelsCategoria,
      datasets: [{
        data: valoresCategoria,
        backgroundColor: coresCategoria
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top' // legenda em cima
        }
      }
    }
  });
</script>


<script>
  new Chart(document.getElementById('resolucaoPrazo'), {
    type: 'doughnut',
    data: {
      labels: ['Dentro do Prazo', 'Fora do Prazo'],
      datasets: [{
        data: [85, 15],
        backgroundColor: ['#28a745', '#dc3545']
      }]
    }
  });
</script>



<script>
  const labelsHora = <?php echo json_encode($labelsHora); ?>;
  const valoresHora = <?php echo json_encode($valoresHora); ?>;

  new Chart(document.getElementById('chamadosHora'), {
    type: 'bar',
    data: {
      labels: labelsHora,
      datasets: [{
        label: 'Chamados',
        data: valoresHora,
        backgroundColor: '#20c997'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
</script>



<script>
  new Chart(document.getElementById('abertosFechados'), {
    type: 'bar',
    data: {
      labels: ['Abr', 'Mai', 'Jun', 'Jul', 'Ago'],
      datasets: [{
        label: 'Abertos',
        data: [10, 14, 13, 18, 16],
        backgroundColor: '#dc3545'
      }, {
        label: 'Fechados',
        data: [8, 12, 11, 15, 14],
        backgroundColor: '#28a745'
      }]
    }
  });
</script>


<script>
    const API_URL = "<?= API_URL ?>";
</script>
<script type="text/javascript" src="<?= BASEJS ?>/home/movimentacao.js"></script>