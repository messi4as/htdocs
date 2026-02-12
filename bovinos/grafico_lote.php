<?php
session_start();
require 'db_connect.php';

// Função para obter opções únicas para os filtros
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

// Função para obter a lista detalhada com CÁLCULO DE IDADE EM MESES via SQL
function getListaAnimaisDetalhada($conn, $local, $lote_filtro, $estratificacao, $situacao_atual)
{
    $sql = "SELECT cod_animal, brinco, lote, sexo, agrupamento, estratificacao, 
            TIMESTAMPDIFF(MONTH, data_nascimento, CURDATE()) AS idade_calculada 
            FROM bovinos 
            WHERE status = 'ATIVO'";

    $conditions = [];
    if ($local != '') $conditions[] = "local LIKE '%$local%'";
    if ($lote_filtro != '') $conditions[] = "lote LIKE '%$lote_filtro%'";
    if ($estratificacao != '') $conditions[] = "estratificacao LIKE '%$estratificacao%'";
    if ($situacao_atual != '') $conditions[] = "situacao_atual LIKE '%$situacao_atual%'";

    if (count($conditions) > 0) {
        $sql .= " AND " . implode(' AND ', $conditions);
    }

    $result = mysqli_query($conn, $sql);
    $animais = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $animais[] = $row;
        }
    }
    return $animais;
}

// Função para obter dados agrupados para o gráfico
function getBovinosPorLote($conn, $local, $lote, $estratificacao, $situacao_atual)
{
    $sql = "SELECT lote, COUNT(*) as quantidade FROM bovinos WHERE status = 'ATIVO'";
    $conditions = [];
    if ($local != '') $conditions[] = "local LIKE '%$local%'";
    if ($lote != '') $conditions[] = "lote LIKE '%$lote%'";
    if ($estratificacao != '') $conditions[] = "estratificacao LIKE '%$estratificacao%'";
    if ($situacao_atual != '') $conditions[] = "situacao_atual LIKE '%$situacao_atual%'";
    if (count($conditions) > 0) $sql .= " AND " . implode(' AND ', $conditions);
    $sql .= " GROUP BY lote ORDER BY lote ASC";
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

$localOptions = getOptions($conn, 'local');
$loteOptions = getOptions($conn, 'lote');
$estratificacaoOptions = getOptions($conn, 'estratificacao');
$situacaoAtualOptions = getOptions($conn, 'situacao_atual');

$local = isset($_GET['local']) ? $_GET['local'] : '';
$lote_filtro = isset($_GET['lote']) ? $_GET['lote'] : '';
$estratificacao = isset($_GET['estratificacao']) ? $_GET['estratificacao'] : '';
$situacao_atual = isset($_GET['situacao_atual']) ? $_GET['situacao_atual'] : '';

$bovinosLoteData = getBovinosPorLote($conn, $local, $lote_filtro, $estratificacao, $situacao_atual);
$listaAnimais = getListaAnimaisDetalhada($conn, $local, $lote_filtro, $estratificacao, $situacao_atual);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <title>GRÁFICO POR LOTE</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            canvas {
                width: 100% !important;
                height: auto !important;
            }
        }

        /* CSS específico para forçar o tamanho pequeno dos botões */
        .dt-buttons {
            display: flex !important;
            justify-content: left !important;
            gap: 15px !important;
            margin-bottom: 20px !important;
        }

        /* Remove a largura total de 100% que o Bootstrap 5 pode estar forçando */
        .dt-buttons .btn {
            width: 150px !important;
            flex: none !important;
        }

        #tabela-animais {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        #tabela-animais thead th {
            text-align: center !important;
            text-transform: uppercase !important;
            background-color: #f8f9fa !important;
        }

        #tabela-animais tbody td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        #loteChart {
            cursor: pointer;
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
                <h4 class="mb-0">GRÁFICO POR LOTE
                    <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
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
                                    <option value="<?= $option ?>" <?= $lote_filtro == $option ? 'selected' : '' ?>><?= $option ?></option>
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
                    <a href="grafico_lote.php" class="btn btn-outline-secondary">Limpar</a>
                </form>

                <div style="position: relative; height:400px; width:100%">
                    <canvas id="loteChart"></canvas>
                </div>

                <div class="alert alert-info" role="alert">
                    <strong>Total de Bovinos:</strong> <?php echo array_sum(array_column($bovinosLoteData, 'quantidade')); ?>
                </div>

                <div id="secao-tabela" class="mt-5" style="display: none;">
                    <hr>
                    <h5 id="titulo-tabela" class="mb-3 text-primary fw-bold text-center"></h5>
                    <div class="table-responsive">
                        <table id="tabela-animais" class="table table-sm table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Brinco</th>
                                    <th>Idade (Meses)</th>
                                    <th>Sexo</th>
                                    <th>Grupo</th>
                                    <th>Estratificação</th>
                                    <th class="no-export">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="corpo-tabela"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('loteChart').getContext('2d');
            const dataFromPhp = <?= json_encode($bovinosLoteData) ?>;
            const todosAnimais = <?= json_encode($listaAnimais) ?>;
            let dataTableInstance = null;

            const labels = dataFromPhp.map(item => item.lote || 'SEM LOTE');
            const values = dataFromPhp.map(item => item.quantidade);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantidade',
                        data: values,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (e, el) => {
                        if (el.length > 0) filtrarTabela(labels[el[0].index]);
                    },
                    layout: {
                        padding: {
                            top: 40
                        }
                    },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: '20%'
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            function filtrarTabela(loteSel) {
                if (dataTableInstance) dataTableInstance.destroy();

                const corpo = document.getElementById('corpo-tabela');
                corpo.innerHTML = '';
                document.getElementById('secao-tabela').style.display = 'block';
                document.getElementById('titulo-tabela').innerText = 'ANIMAIS NO LOTE: ' + loteSel;

                const filtrados = todosAnimais.filter(a => (a.lote || 'SEM LOTE') === loteSel);

                filtrados.forEach(a => {
                    corpo.innerHTML += `<tr>
                    <td>${a.brinco}</td>
                    <td>${a.idade_calculada}</td>
                    <td>${a.sexo}</td>
                    <td>${a.agrupamento || '-'}</td>
                    <td>${a.estratificacao}</td>
                    <td><a href="view_animal.php?id=${a.cod_animal}" class="btn btn-secondary btn-sm">Visualizar</a></td>
                </tr>`;
                });

                // Configuração do DataTable
                dataTableInstance = $('#tabela-animais').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                    },
                    dom: 'Bfrtip',
                    paging: false,
                    info: true,
                    autoWidth: false,
                    buttons: [{
                            extend: 'excelHtml5',
                            text: 'Exportar Excel',
                            className: 'btn btn-success btn-sm', // Mantém o estilo pequeno
                            title: 'RELATÓRIO DE BOVINOS - LOTE: ' + loteSel,
                            messageBottom: 'Total de Animais: ' + filtrados.length,
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Gerar PDF',
                            className: 'btn btn-danger btn-sm',
                            title: 'RELATÓRIO DE BOVINOS - LOTE: ' + loteSel,
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            },
                            customize: function(doc) {
                                doc.content[1].table.widths = ['10%', '10%', '10%', '35%', '35%'];
                                doc.content[1].alignment = 'center';

                                doc.content.push({
                                    text: '\nTotal de Animais no Lote: ' + filtrados.length,
                                    bold: true,
                                    alignment: 'left',
                                    margin: [0, 10, 0, 0]
                                });

                                var rowCount = doc.content[1].table.body.length;
                                for (var i = 0; i < rowCount; i++) {
                                    for (var j = 0; j < doc.content[1].table.body[i].length; j++) {
                                        doc.content[1].table.body[i][j].alignment = 'center';
                                    }
                                }
                            }
                        }
                    ]
                });

                setTimeout(() => {
                    dataTableInstance.columns.adjust().draw();
                }, 100);
                document.getElementById('secao-tabela').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    </script>
</body>

</html>