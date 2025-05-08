<?php
require 'db_connect.php';

$status_filtro = isset($_GET['status']) ? $_GET['status'] : '';
$nome = isset($_GET['nome']) ? $_GET['nome'] : '';

// Consulta para obter os cartões com filtros, garantindo que apenas o último status de cada cartão seja exibido
$sql = "SELECT c.numero, u.status_atualizado AS status, u.nome, u.funcao, u.responsavel, u.data_utilizacao AS data_entrega
        FROM cartoes c
        LEFT JOIN (
            SELECT cartao_id, MAX(data_utilizacao) AS ultima_utilizacao
            FROM utilizacoes
            GROUP BY cartao_id
        ) ult ON c.id = ult.cartao_id
        LEFT JOIN utilizacoes u ON c.id = u.cartao_id AND u.data_utilizacao = ult.ultima_utilizacao
        WHERE 1=1 and nome is not null";

if ($status_filtro) {
    $sql .= " AND u.status_atualizado = '$status_filtro'";
}

if ($nome) {
    $sql .= " AND u.nome LIKE '%$nome%'";
}

$result = $conn->query($sql);
$quantidade = mysqli_num_rows($result);

// Consulta para obter os nomes únicos
$nomes_result = $conn->query("SELECT DISTINCT nome FROM utilizacoes");

// Contagem de cartões por status
$estoque = $conn->query("SELECT COUNT(*) AS count FROM cartoes WHERE status = 'estoque'")->fetch_assoc()['count'];
$entregue = $conn->query("SELECT COUNT(*) AS count FROM cartoes WHERE status = 'entregue'")->fetch_assoc()['count'];
$devolvido = $conn->query("SELECT COUNT(*) AS count FROM cartoes WHERE status = 'devolvido'")->fetch_assoc()['count'];
$extraviado = $conn->query("SELECT COUNT(*) AS count FROM cartoes WHERE status = 'extraviado'")->fetch_assoc()['count'];
$utilizado = $conn->query("SELECT COUNT(*) AS count FROM cartoes WHERE status = 'utilizado'")->fetch_assoc()['count'];
$fechamento = $conn->query("SELECT COUNT(*) AS count FROM cartoes WHERE status = 'fechamento'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <title>Visualização de Cartões</title>

    <style>
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

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
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h2>CARTÕES ALIMENTAÇÃO FAZENDA ROSADA
                                <div class="float-end"></div>
                            </h2>

                        </div>

                        <canvas id="myChart" width="400" height="200"></canvas>

                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                Últimas Movimentações: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <!-- Filtros -->
                            <form method="GET" action="">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="status">Status:</label>
                                        <select id="status" name="status" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="estoque" <?= $status_filtro == 'estoque' ? 'selected' : '' ?>>Estoque</option>
                                            <option value="entregue" <?= $status_filtro == 'entregue' ? 'selected' : '' ?>>Entregue</option>
                                            <option value="devolvido" <?= $status_filtro == 'devolvido' ? 'selected' : '' ?>>Devolvido</option>
                                            <option value="extraviado" <?= $status_filtro == 'extraviado' ? 'selected' : '' ?>>Extraviado</option>
                                            <option value="utilizado" <?= $status_filtro == 'utilizado' ? 'selected' : '' ?>>Utilizado</option>
                                            <option value="fechamento" <?= $status_filtro == 'fechamento' ? 'selected' : '' ?>>Fechamento</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="nome">Nome:</label>
                                        <select id="nome" name="nome" class="form-control">
                                            <option value="">Todos</option>
                                            <?php while ($row = $nomes_result->fetch_assoc()): ?>
                                                <option value="<?= $row['nome'] ?>" <?= $nome == $row['nome'] ? 'selected' : '' ?>><?= $row['nome'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary form-control">Filtrar</button>
                                    </div>
                                </div>
                            </form>
                            <!-- Tabela de Cartões -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">NÚMERO</th>
                                        <th style="text-align: center;">NOME DO RECEBEDOR</th>
                                        <th style="text-align: center;">FUNÇÃO / SERVIÇO</th>
                                        <th style="text-align: center;">DATA</th>
                                        <th style="text-align: center;">RESPONSÁVEL</th>
                                        <th style="text-align: center;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td style='text-align: center; vertical-align: middle;''>" . $row['numero'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;''>" . $row['nome'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;''>" . $row['funcao'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . date('d/m/Y', strtotime($row['data_entrega'])) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['responsavel'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['status'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>Nenhum cartão encontrado</td></tr>";
                                    }
                                    ?>
                                </tbody>
                                <!--
                            <tfoot>
                                <tr>
                                    <th style="text-align: center;">Total</th>
                                    <th style="text-align: center;"><?php echo $quantidade; ?></th>
                                    <th style="text-align: center;"><?php echo $quantidade; ?></th>
                                    <th style="text-align: center;"><?php echo $quantidade; ?></th>
                                    <th style="text-align: center;"><?php echo $quantidade; ?></th>
                                    <th style="text-align: center;"><?php echo $quantidade; ?></th>
                                </tr>
                            </tfoot>
                            -->
                            </table>



                            <!-- Gráfico -->

                            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                            <script>
                                // Dados do gráfico
                                var data = {
                                    labels: ["Estoque", "Entregue", "Devolvido", "Extraviado", "Utilizado", "Fechamento"],
                                    datasets: [{
                                        label: 'Resumo',
                                        data: [<?php echo $estoque; ?>, <?php echo $entregue; ?>, <?php echo $devolvido; ?>, <?php echo $extraviado; ?>, <?php echo $utilizado; ?>, <?php echo $fechamento; ?>],
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.3)',
                                            'rgba(54, 162, 235, 0.3)',
                                            'rgba(255, 206, 86, 0.3)',
                                            'rgba(75, 192, 192, 0.3)',
                                            'rgba( 192, 192, 0.2)'
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(75, 192, 81, 1)',
                                            'rgba(184, 192, 75, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                };

                                // Configuração do gráfico
                                var config = {
                                    type: 'bar',
                                    data: data,
                                    options: {
                                        layout: {
                                            padding: {
                                                top: 20 // Adiciona espaço extra na parte superior
                                            }
                                        },
                                        plugins: {
                                            datalabels: {
                                                anchor: 'end',
                                                align: 'top',
                                                formatter: (value) => value,
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
                                };

                                // Renderiza o gráfico
                                var myChart = new Chart(
                                    document.getElementById('myChart'),
                                    config
                                );
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>