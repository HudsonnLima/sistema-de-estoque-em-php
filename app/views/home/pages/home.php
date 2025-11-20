
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  canvas {
    max-height: 220px !important;
    /* ajuste aqui */
  }
</style>
<h2 class="mb-4 text-left">📊 Dashboard estoque</h2>

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

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Compras por fornecedor (Últimos 6 meses)</h5>
        <canvas id="fornecedores"></canvas>
      </div>
    </div>
  </div>

  <div class="col">
    <div class="card shadow rounded-3 h-100">
      <div class="card-body">
        <h5 class="card-title">Produtos x Saídas</h5> <canvas id="saidaProdutos"></canvas>
      </div>
    </div>
  </div>

</div>

<script>
  const API_URL = "<?= API_URL ?>";
</script>
<script type="text/javascript" src="<?= BASEJS ?>/home/movimentacao.js"></script>
<script type="text/javascript" src="<?= BASEJS ?>/home/fornecedores.js"></script>
<script type="text/javascript" src="<?= BASEJS ?>/home/saidasfuncionarios.js"></script>