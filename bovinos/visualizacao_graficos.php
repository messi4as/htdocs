<?php
session_start();
require 'db_connect.php';

// Função para obter opções únicas de uma coluna
function getOptions($conn, $column)
{
    $options = [];
    $sql = "SELECT DISTINCT $column FROM bovinos WHERE status = 'ATIVO'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row[$column];
    }
    return $options;
}

$localOptions = getOptions($conn, 'local');
$loteOptions = getOptions($conn, 'lote');
$agrupamentoOptions = getOptions($conn, 'agrupamento');
$situacaoAtualOptions = getOptions($conn, 'situacao_atual');

// Função para obter dados dos bovinos com filtros
function getBovinosData($conn, $local, $lote, $agrupamento, $situacao_atual)
{
    $sql = "SELECT local, lote, agrupamento, situacao_atual, COUNT(*) as quantidade 
            FROM bovinos 
            WHERE status = 'ATIVO'";
    $conditions = [];
    if ($local != '') {
        $conditions[] = "local LIKE '%$local%'";
    }
    if ($lote != '') {
        $conditions[] = "lote LIKE '%$lote%'";
    }
    if ($agrupamento != '') {
        $conditions[] = "agrupamento LIKE '%$agrupamento%'";
    }
    if ($situacao_atual != '') {
        $conditions[] = "situacao_atual LIKE '%$situacao_atual%'";
    }
    if (count($conditions) > 0) {
        $sql .= " AND " . implode(' AND ', $conditions);
    }
    $sql .= " GROUP BY agrupamento";
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

$local = isset($_GET['local']) ? $_GET['local'] : '';
$lote = isset($_GET['lote']) ? $_GET['lote'] : '';
$agrupamento = isset($_GET['agrupamento']) ? $_GET['agrupamento'] : '';
$situacao_atual = isset($_GET['situacao_atual']) ? $_GET['situacao_atual'] : '';

$bovinosData = getBovinosData($conn, $local, $lote, $agrupamento, $situacao_atual);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <title>VISUALIZAÇÃO DE BOVINOS - GRÁFICOS</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            .print-only {
                display: table-cell;
            }

            canvas {
                width: 100% !important;
                height: auto !important;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        Chart.register(ChartDataLabels);
    </script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>VISUALIZAÇÃO DE BOVINOS - GRÁFICOS <div class="float-end">
                                    <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>

                            </h4>
                        </div>
                        <div class="card-body">

                            <form method="GET" action="">
                                <div class="input-group mb-3">
                                    <select name="local" class="form-control">
                                        <option value="">Local</option>
                                        <?php foreach ($localOptions as $option) : ?>
                                            <option value="<?= $option ?>" <?= $local == $option ? 'selected' : '' ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select> &nbsp; &nbsp;
                                    <select name="lote" class="form-control">
                                        <option value="">Lote</option>
                                        <?php foreach ($loteOptions as $option) : ?>
                                            <option value="<?= $option ?>" <?= $lote == $option ? 'selected' : '' ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <select name="agrupamento" class="form-control">
                                        <option value="">Grupo</option>
                                        <?php foreach ($agrupamentoOptions as $option) : ?>
                                            <option value="<?= $option ?>" <?= $agrupamento == $option ? 'selected' : '' ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select> &nbsp; &nbsp;
                                    <select name="situacao_atual" class="form-control">
                                        <option value="">Situação Atual</option>
                                        <?php foreach ($situacaoAtualOptions as $option) : ?>
                                            <option value="<?= $option ?>" <?= $situacao_atual == $option ? 'selected' : '' ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button class="btn btn-primary" type="submit">Filtrar</button>
                        </div>



                        <canvas id="bovinosChart"></canvas>

                        </form>
                        <?php
                        $local = isset($_GET['local']) ? $_GET['local'] : '';
                        $lote = isset($_GET['lote']) ? $_GET['lote'] : '';
                        $agrupamento = isset($_GET['agrupamento']) ? $_GET['agrupamento'] : '';
                        $situacao_atual = isset($_GET['situacao_atual']) ? $_GET['situacao_atual'] : '';

                        $sql = "SELECT brinco, 
                       TIMESTAMPDIFF(MONTH, data_nascimento, CURDATE()) AS idade, 
                       agrupamento, 
                       situacao_atual, 
                       local, 
                       lote 
                FROM bovinos
                WHERE status = 'ATIVO'";
                        $conditions = [];
                        if ($local != '') {
                            $conditions[] = "local LIKE '%$local%'";
                        }
                        if ($lote != '') {
                            $conditions[] = "lote LIKE '%$lote%'";
                        }
                        if ($agrupamento != '') {
                            $conditions[] = "agrupamento LIKE '%$agrupamento%'";
                        }
                        if ($situacao_atual != '') {
                            $conditions[] = "situacao_atual LIKE '%$situacao_atual%'";
                        }
                        if (count($conditions) > 0) {
                            $sql .= " AND " . implode(' AND ', $conditions);
                        }
                        $result = mysqli_query($conn, $sql);
                        $quantidade = mysqli_num_rows($result);
                        ?>

                        <div class="alert alert-info" role="alert">
                            Quantidade de Bovinos: <?php echo $quantidade; ?>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('bovinosChart').getContext('2d');
            var bovinosData = <?= json_encode($bovinosData) ?>;

            console.log(bovinosData); // Verifique os dados no console

            var labels = bovinosData.map(function(item) {
                return item.agrupamento;
            });
            var data = bovinosData.map(function(item) {
                return item.quantidade;
            });

            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantidade de Bovinos',
                        data: data,
                        backgroundColor: 'rgba(207, 244, 252, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    layout: {
                        padding: {
                            top: 20 // Adiciona espaço extra na parte superior
                        }
                    },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            formatter: function(value) {
                                return value;
                            },
                            color: 'black',
                            font: {
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: false // Oculta a legenda
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    </script>
</body>

</html>