<?php
session_start();
require 'db_connect.php';

// Função para obter opções únicas de uma coluna
function getOptions($conn, $column)
{
    $options = [];
    $sql = "SELECT DISTINCT $column FROM bovinos WHERE status = 'ATIVO' AND $column IS NOT NULL AND $column != '' ORDER BY $column ASC";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row[$column];
    }
    return $options;
}

$localOptions = getOptions($conn, 'local');
$loteOptions = getOptions($conn, 'lote');
$estratificacaoOptions = getOptions($conn, 'estratificacao');
$situacaoAtualOptions = getOptions($conn, 'situacao_atual');

$local = isset($_GET['local']) ? $_GET['local'] : '';
$lote = isset($_GET['lote']) ? $_GET['lote'] : '';
$estratificacao = isset($_GET['estratificacao']) ? $_GET['estratificacao'] : '';
$situacao_atual = isset($_GET['situacao_atual']) ? $_GET['situacao_atual'] : '';

// Função para obter dados agrupados para o gráfico
function getBovinosData($conn, $local, $lote, $estratificacao, $situacao_atual)
{
    $sql = "SELECT estratificacao, COUNT(*) as quantidade FROM bovinos WHERE status = 'ATIVO'";
    $conditions = [];
    if ($local != '') $conditions[] = "local LIKE '%$local%'";
    if ($lote != '') $conditions[] = "lote LIKE '%$lote%'";
    if ($estratificacao != '') $conditions[] = "estratificacao LIKE '%$estratificacao%'";
    if ($situacao_atual != '') $conditions[] = "situacao_atual LIKE '%$situacao_atual%'";
    
    if (count($conditions) > 0) $sql .= " AND " . implode(' AND ', $conditions);
    
    $sql .= " GROUP BY estratificacao ORDER BY FIELD(estratificacao, 
        'MACHO, 0 A 12 MESES', 'FÊMEA, 0 A 12 MESES', 
        'MACHO, 13 A 24 MESES', 'FÊMEA, 13 A 24 MESES', 
        'MACHO, 25 A 36 MESES', 'FÊMEA, 25 A 36 MESES', 
        'MACHO, ACIMA DE 36 MESES', 'FÊMEA, ACIMA DE 36 MESES')";
    
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

$bovinosData = getBovinosData($conn, $local, $lote, $estratificacao, $situacao_atual);
$quantidade_total = array_sum(array_column($bovinosData, 'quantidade'));
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>GRÁFICO POR ESTRATIFICAÇÃO</title>
    <style>
        @media print {
            .no-print { display: none; }
            canvas { width: 100% !important; height: auto !important; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="mb-0">GRÁFICO POR ESTRATIFICAÇÃO
                    <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                    <a href="grafico_lote.php" class="btn btn-warning me-2 float-end"><span class="bi bi-bar-chart"></span>&nbsp;Ver Gráfico por Lote</a>
                </h4>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="mb-4 no-print">
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <select name="local" class="form-select">
                                <option value="">Local</option>
                                <?php foreach ($localOptions as $option) : ?>
                                    <option value="<?= $option ?>" <?= $local == $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="lote" class="form-select">
                                <option value="">Lote</option>
                                <?php foreach ($loteOptions as $option) : ?>
                                    <option value="<?= $option ?>" <?= $lote == $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="estratificacao" class="form-select">
                                <option value="">Estratificação</option>
                                <?php foreach ($estratificacaoOptions as $option) : ?>
                                    <option value="<?= $option ?>" <?= $estratificacao == $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="situacao_atual" class="form-select">
                                <option value="">Situação Atual</option>
                                <?php foreach ($situacaoAtualOptions as $option) : ?>
                                    <option value="<?= $option ?>" <?= $situacao_atual == $option ? 'selected' : '' ?>><?= $option ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a href="grafico_estratificacao.php" class="btn btn-outline-secondary">Limpar</a>
                </form>

                <div style="position: relative; height:400px; width:100%; max-width: 900px; margin: 0 auto;">
                    <canvas id="bovinosChart"></canvas>
                </div>

                <div class="alert alert-info mt-4" role="alert">
                    <strong>Total de Bovinos:</strong> <?php echo $quantidade_total; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bovinosChart').getContext('2d');
            const bovinosData = <?= json_encode($bovinosData) ?>;

            const labels = bovinosData.map(item => item.estratificacao);
            const data = bovinosData.map(item => item.quantidade);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantidade',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { top: 40 }
                    },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            font: { weight: 'bold', size: 14 },
                            color: 'black'
                        },
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: '20%'
                        },
                        x: {
                            ticks: { font: { weight: 'bold' } }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    </script>
</body>
</html>