<?php
require 'db_connect.php';

$status_filtro = isset($_GET['status']) ? $_GET['status'] : '';
$nome_filtro = isset($_GET['nome']) ? $_GET['nome'] : '';

// 1. Consulta Principal com Consultas Preparadas
$sql = "
    SELECT
        c.numero,
        u.status_atualizado AS status,
        u.nome,
        u.funcao,
        u.responsavel,
        u.data_utilizacao AS data_entrega
    FROM
        cartoes c
    LEFT JOIN (
        SELECT
            cartao_id,
            MAX(id) AS ultimo_id_utilizacao
        FROM
            utilizacoes
        GROUP BY
            cartao_id
    ) ult ON c.id = ult.cartao_id
    LEFT JOIN
        utilizacoes u ON u.id = ult.ultimo_id_utilizacao
    WHERE
        u.nome IS NOT NULL";

// Adicionar filtros de forma segura
if ($status_filtro) {
    $sql .= " AND u.status_atualizado = ?";
}

if ($nome_filtro) {
    $sql .= " AND u.nome LIKE ?";
}

// Preparar a consulta
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Vincular os parâmetros de forma segura
    $params = [];
    $types = '';
    
    if ($status_filtro) {
        $params[] = &$status_filtro;
        $types .= 's';
    }
    if ($nome_filtro) {
        $nome_like = "%" . $nome_filtro . "%";
        $params[] = &$nome_like;
        $types .= 's';
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $quantidade = $result->num_rows;
} else {
    // Lidar com erro na preparação da consulta
    $quantidade = 0;
}

// 2. Consulta para Nomes Únicos
$nomes_result = $conn->query("SELECT DISTINCT nome FROM utilizacoes WHERE nome IS NOT NULL ORDER BY nome");

// 3. Contagem de cartões por status
// Esta é a parte mais crítica, pois a contagem atual não está correta.
// Precisamos contar os status com base na mesma lógica da consulta principal (o último status de cada cartão).
$contagem_sql = "
    SELECT
        u.status_atualizado,
        COUNT(u.status_atualizado) as count
    FROM
        cartoes c
    LEFT JOIN (
        SELECT
            cartao_id,
            MAX(id) AS ultimo_id_utilizacao
        FROM
            utilizacoes
        GROUP BY
            cartao_id
    ) ult ON c.id = ult.cartao_id
    LEFT JOIN
        utilizacoes u ON u.id = ult.ultimo_id_utilizacao
    WHERE
        u.nome IS NOT NULL
    GROUP BY
        u.status_atualizado
";

$contagem_result = $conn->query($contagem_sql);

$counts = [
    'estoque' => 0,
    'entregue' => 0,
    'devolvido' => 0,
    'extraviado' => 0,
    'utilizado' => 0,
    'fechamento' => 0
];

if ($contagem_result) {
    while ($row = $contagem_result->fetch_assoc()) {
        $counts[$row['status_atualizado']] = $row['count'];
    }
}

// Agora você pode usar os valores do array $counts no gráfico
$estoque = $counts['estoque'];
$entregue = $counts['entregue'];
$devolvido = $counts['devolvido'];
$extraviado = $counts['extraviado'];
$utilizado = $counts['utilizado'];
$fechamento = $counts['fechamento'];

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
                                            <?php if ($nomes_result && $nomes_result->num_rows > 0) : ?>
                                                <?php while ($row = $nomes_result->fetch_assoc()) : ?>
                                                    <option value="<?= htmlspecialchars($row['nome']) ?>" <?= $nome_filtro == $row['nome'] ? 'selected' : '' ?>><?= htmlspecialchars($row['nome']) ?></option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary form-control">Filtrar</button>
                                    </div>
                                </div>
                            </form>
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
                                    if (isset($result) && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['numero']) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['nome']) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['funcao']) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . htmlspecialchars(date('d/m/Y', strtotime($row['data_entrega']))) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['responsavel']) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['status']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>Nenhum cartão encontrado</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                            <script>
                                var data = {
                                    labels: ["Estoque", "Entregue", "Devolvido", "Extraviado", "Utilizado", "Fechamento"],
                                    datasets: [{
                                        label: 'Resumo',
                                        data: [<?php echo $estoque; ?>, <?php echo $entregue; ?>, <?php echo $devolvido; ?>, <?php echo $extraviado; ?>, <?php echo $utilizado; ?>, <?php echo $fechamento; ?>],
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.3)', 'rgba(54, 162, 235, 0.3)', 'rgba(255, 206, 86, 0.3)',
                                            'rgba(75, 192, 192, 0.3)', 'rgba(192, 192, 0.2)', 'rgba(184, 192, 75, 0.3)'
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)', 'rgba(75, 192, 81, 1)', 'rgba(184, 192, 75, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                };
                                var config = {
                                    type: 'bar',
                                    data: data,
                                    options: {
                                        layout: {
                                            padding: {
                                                top: 20
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
                                                display: false
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
                                var myChart = new Chart(document.getElementById('myChart'), config);
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