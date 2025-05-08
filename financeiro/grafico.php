<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Obter os dados dos parâmetros GET
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$nome = isset($_GET['responsavel']) ? $_GET['responsavel'] : '';

// Buscar todos os responsáveis distintos do banco de dados
$responsaveis_query = "SELECT DISTINCT responsavel FROM financeiro";
$responsaveis_result = $conn->query($responsaveis_query);

$sql = "SELECT * FROM financeiro WHERE 1=1"; // Para facilitar a inclusão das condições
if ($nome != '') {
    $sql .= " AND responsavel = '$nome'";
}
if ($data_inicio && $data_fim) {
    $sql .= " AND data BETWEEN '$data_inicio' AND '$data_fim'";
}
$sql .= " ORDER BY data";

$result = $conn->query($sql);
$financeiro = mysqli_query($conn, $sql);
$quantidade = mysqli_num_rows($financeiro);

$total_debito = 0;
$total_credito = 0;

function converterMoedaParaNumero($valor)
{
    $valor = str_replace('R$', '', $valor);
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return floatval($valor);
}

while ($row = mysqli_fetch_assoc($financeiro)) {
    $valor = converterMoedaParaNumero($row['valor']);
    if ($row['tipo'] == 'DÉBITO') {
        $total_debito += $valor;
    } elseif ($row['tipo'] == 'CRÉDITO') {
        $total_credito += $valor;
    }
}

$saldo = $total_credito - $total_debito;

function formatarNumeroParaMoeda($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Adicionando Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> <!-- Adicionando plugin para exibir valores -->

    <script>
        Chart.register(ChartDataLabels);
    </script>

    <title>Financeiro</title>

    <style>
        .form-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12px;
            /* Ajuste do tamanho da fonte */
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 50%;
            font-size: 09px;
            /* Ajuste do tamanho da fonte */
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            /* Ajuste do tamanho da fonte */
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Estilos para impressão */
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

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px;
                /* Ajuste do tamanho da fonte para impressão */
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                word-wrap: break-word;
            }

            th {
                background-color: #f2f2f2;
            }
        }
    </style>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <?php include('/xampp/htdocs/mensagem.php'); ?>
        <?php if (isset($_GET['import_success']) && $_GET['import_success'] == 'true'): ?>
            <div class="alert alert-success">
                Dados importados com sucesso! Linhas importadas: <?= htmlspecialchars($_GET['linhas_importadas']) ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>FINANCEIRO M2
                                <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                                <br>
                            </h4>
                        </div>

                        <div class="card-body">
                            <!-- Filtros -->
                            <form method="GET" action="" class="mb-3">
                                <label class="form-label">Filtros:</label>
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label for="data_inicio" class="form-label">Data Início:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_fim" class="form-label">Data Fim:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="responsavel" class="form-label">Responsável:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="<?= $data_inicio ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" id="data_fim" name="data_fim" class="form-control" value="<?= $data_fim ?>">
                                    </div>
                                    <div class="col-md-3" style="width:300px">
                                        <select id="responsavel" name="responsavel" class="form-control" style="font-size: 15px;">
                                            <option value="">Selecione um responsável</option>
                                            <?php while ($row = $responsaveis_result->fetch_assoc()): ?>
                                                <option value="<?= $row['responsavel'] ?>" <?= $nome == $row['responsavel'] ? 'selected' : '' ?>>
                                                    <?= $row['responsavel'] ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3" style="width:150px">
                                        <button type="submit" class="btn btn-primary form-control"><span class="bi bi-funnel-fill"></span>&nbsp;Filtrar</button>
                                    </div>
                                </div>
                            </form>


                            <div class="alert alert-info" role="alert">
                                QUANTIDADE DE PAGAMENTOS CADASTRADOS: <?php echo $quantidade; ?>
                            </div>
                            <br>

                            <!-- Adicionando o canvas para o gráfico -->
                            <canvas id="financeiroChart" width="400" height="200"></canvas>
                            <br>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">DATA</th>
                                        <th style="text-align: center; width:500px">DESCRIÇÃO</th>
                                        <th style="text-align: center; width:130px">VALOR</th>
                                        <th style="text-align: center; width:500px">FORMA DE PAGAMENTO</th>
                                        <!--
            <th style="text-align: center;">DOCUMENTO</th>
            <th style="text-align: center;">COMPROVANTE</th>
            <th style="text-align: center;">AÇÕES</th>
            -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                                                <td style="text-align: left; vertical-align: middle;"><?= $row['descricao']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['valor']; ?></td>
                                                <td style="text-align: left; vertical-align: middle;"><?= $row['forma_pagamento']; ?></td>
                                                <!--
                <td style="text-align: center; vertical-align: middle;">
                    <?php
                                            $documentos = json_decode($row['documento'], true);
                                            if (!empty($documentos) && is_array($documentos)):
                                                foreach ($documentos as $documento):
                    ?>
                            <a href="<?= htmlspecialchars($documento); ?>" target="_blank"><span class="bi bi-box-arrow-down"></span>&nbsp;Documento Anexado</a><br>
                    <?php
                                                endforeach;
                                            else:
                    ?>
                        <p>Nenhum documento Anexado.</p>
                    <?php
                                            endif;
                    ?>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <?php
                                            $comprovantes = json_decode($row['comprovante'], true);
                                            if (!empty($comprovantes) && is_array($comprovantes)):
                                                foreach ($comprovantes as $comprovante):
                    ?>
                            <a href="<?= htmlspecialchars($comprovante); ?>" target="_blank"><span class="bi bi-box-arrow-down"></span>&nbsp;Comprovante Anexado</a><br>
                    <?php
                                                endforeach;
                                            else:
                    ?>
                        <p>Nenhum Comprovante Anexado.</p>
                    <?php
                                            endif;
                    ?>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <a href="edit_pagamentos.php?id=<?= $row['cod_financeiro'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                </td>
                -->
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" style="text-align: center;">Nenhum Pagamento encontrado</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="alert alert-info" role="alert">
                                QUANTIDADE DE PAGAMENTOS CADASTRADOS: <?php echo $quantidade; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            // Dados para o gráfico
            var ctx = document.getElementById('financeiroChart').getContext('2d');
            var financeiroChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['DÉBITO', 'CRÉDITO', 'SALDO'],
                    datasets: [{
                        label: 'Valores em R$',
                        data: [<?= $total_debito ?>, <?= $total_credito ?>, <?= $saldo ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.3)',
                            'rgba(75, 192, 192, 0.3)',
                            'rgba(54, 162, 235, 0.3)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
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
                            color: 'black',
                            font: {
                                weight: 'bold'
                            },
                            formatter: function(value) {
                                return 'R$ ' + value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
                }
            });
        </script>
</body>

</html>