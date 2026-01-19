<?php
session_start();
require 'db_connect.php';

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

/**
 * Função para obter opções únicas de uma coluna.
 * @param mysqli $conn A conexão com o banco de dados.
 * @param string $column O nome da coluna.
 * @return array Um array com as opções únicas.
 */
function getOptions($conn, $column)
{
    $options = [];
    $sql = "SELECT DISTINCT $column FROM bovinos WHERE status = 'ATIVO' ORDER BY $column";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $options[] = $row[$column];
        }
    }
    return $options;
}

$localOptions = getOptions($conn, 'local');
$loteOptions = getOptions($conn, 'lote');
$agrupamentoOptions = getOptions($conn, 'agrupamento');
$situacaoAtualOptions = getOptions($conn, 'situacao_atual');

// Coleta e sanitiza os dados do formulário
$local = isset($_GET['local']) ? trim($_GET['local']) : '';
$lote = isset($_GET['lote']) ? trim($_GET['lote']) : '';
$agrupamento = isset($_GET['agrupamento']) ? trim($_GET['agrupamento']) : '';
$situacao_atual = isset($_GET['situacao_atual']) ? trim($_GET['situacao_atual']) : '';

// Prepara a consulta SQL com Prepared Statements
$sql = "SELECT brinco, 
               TIMESTAMPDIFF(MONTH, data_nascimento, CURDATE()) AS idade, 
               agrupamento, 
               situacao_atual, 
               local, 
               lote 
        FROM bovinos
        WHERE status = 'ATIVO'";

$conditions = [];
$params = [];
$types = '';

if (!empty($local)) {
    $conditions[] = "local LIKE ?";
    $params[] = "%$local%";
    $types .= 's';
}
if (!empty($lote)) {
    $conditions[] = "lote LIKE ?";
    $params[] = "%$lote%";
    $types .= 's';
}
if (!empty($agrupamento)) {
    $conditions[] = "agrupamento LIKE ?";
    $params[] = "%$agrupamento%";
    $types .= 's';
}
if (!empty($situacao_atual)) {
    $conditions[] = "situacao_atual LIKE ?";
    $params[] = "%$situacao_atual%";
    $types .= 's';
}

if (count($conditions) > 0) {
    $sql .= " AND " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY brinco DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $quantidade = $result->num_rows;
} else {
    // Em caso de erro na preparação da query
    $quantidade = 0;
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>VISUALIZAÇÃO DE BOVINOS</title>

    <style>
        /* Estilos gerais */
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
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
        }

        .form-select,
        .form-control {
            margin-right: 10px;
        }

        @media (min-width: 768px) {
            .input-group.mb-3 {
                flex-wrap: nowrap;
            }
        }

        /* Estilos para Impressão */
        @media print {
            body {
                font-size: 10pt;
            }

            .no-print,
            .btn,
            form,
            #mensagem,
            .modal,
            .card-header a,
            .float-end {
                display: none !important;
            }

            .container,
            .card,
            .card-body {
                padding: 0;
                margin: 0;
                box-shadow: none;
                border: none;
                width: 100% !important;
                max-width: none !important;
            }

            h4 {
                text-align: auto;
                margin: 6px auto;
            }

            .table-container {
                width: 95%;
                margin: 0 auto;
                overflow-x: visible !important;
            }

            table {
                width: 100%;
                font-size: 9pt;
                page-break-inside: auto;
            }

            th,
            td {
                padding: 4px;
                word-wrap: break-word;
            }

            .alert-info {
                display: block !important;
                width: 95%;
                margin: 0 auto 5px auto;
                text-align: auto;
            }
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div id="mensagem"><?php include('mensagem.php'); ?></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>VISUALIZAÇÃO DE BOVINOS PARA CONFERÊNCIA</h4>
                        <div class="float-end no-print">
                            <button onclick="window.print()" class="btn btn-info me-2"><span class="bi-printer-fill"></span>&nbsp;Imprimir</button>
                            <a href="importar.php" class="btn btn-success me-2"><span class="bi bi-file-earmark-excel"></span>&nbsp;Importar Pesos</a>
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#ocorrenciaModal">Adicionar Ocorrência</button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#atualizarModal">Atualizar Local e Lote</button>


                        </div>
                    </div>

                    <div class="card-body">
                        <form method="GET" action="" class="mb-3 no-print">
                            <div class="input-group">
                                <select name="local" class="form-select">
                                    <option value="">Local</option>
                                    <?php foreach ($localOptions as $option) : ?>
                                        <option value="<?= htmlspecialchars($option) ?>" <?= $local == $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="lote" class="form-select">
                                    <option value="">Lote</option>
                                    <?php foreach ($loteOptions as $option) : ?>
                                        <option value="<?= htmlspecialchars($option) ?>" <?= $lote == $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="agrupamento" class="form-select">
                                    <option value="">Grupo</option>
                                    <?php foreach ($agrupamentoOptions as $option) : ?>
                                        <option value="<?= htmlspecialchars($option) ?>" <?= $agrupamento == $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="situacao_atual" class="form-select">
                                    <option value="">Situação Atual</option>
                                    <?php foreach ($situacaoAtualOptions as $option) : ?>
                                        <option value="<?= htmlspecialchars($option) ?>" <?= $situacao_atual == $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-primary" type="submit">Filtrar</button>
                            </div>
                        </form>

                        <div class="alert alert-info" role="alert">
                            Quantidade de Bovinos Ativos: <strong><?php echo number_format($quantidade, 0, ',', '.'); ?></strong>
                        </div>

                        <div class="table-container">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>BRINCO</th>
                                        <th>IDADE (MESES)</th>
                                        <th>GRUPO</th>
                                        <th>SITUAÇÃO ATUAL</th>
                                        <th>LOCAL</th>
                                        <th>LOTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['brinco']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['idade']) . " meses</td>";
                                            echo "<td>" . htmlspecialchars($row['agrupamento']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['situacao_atual']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['local']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['lote']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>Nenhum bovino encontrado.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ocorrenciaModal" tabindex="-1" aria-labelledby="ocorrenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ocorrenciaModalLabel">Adicionar Ocorrência</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ocorrenciaForm" method="POST">
                        <div class="mb-3">
                            <label for="brincos_ocorrencia" class="form-label">Brincos (separados por vírgulas)</label>
                            <input type="text" class="form-control" id="brincos_ocorrencia" name="brincos" required>
                        </div>
                        <div class="mb-3">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control" id="data" name="data" required>
                        </div>
                        <div class="mb-3">
                            <label for="local" class="form-label">Local</label>
                            <input type="text" class="form-control" id="local_ocorrencia" name="local" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" required>
                        </div>
                        <div class="mb-3">
                            <label for="peso" class="form-label">Peso</label>
                            <input type="text" class="form-control" id="peso" name="peso">
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="atualizarModal" tabindex="-1" aria-labelledby="atualizarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="atualizarModalLabel">Atualizar Local e Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="atualizarForm" method="POST">
                        <div class="mb-3">
                            <label for="brincos_atualizar" class="form-label">Brincos (separados por vírgulas)</label>
                            <input type="text" class="form-control" id="brincos_atualizar" name="brincos" required>
                        </div>
                        <div class="mb-3">
                            <label for="novo_local" class="form-label">Novo Local</label>
                            <input type="text" class="form-control" id="novo_local" name="novo_local" required>
                        </div>
                        <div class="mb-3">
                            <label for="novo_lote" class="form-label">Novo Lote</label>
                            <input type="text" class="form-control" id="novo_lote" name="novo_lote" required>
                        </div>
                        <button type="submit" class="btn btn-secondary">Atualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Lógica AJAX para o formulário de Atualização
            $('#atualizarForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'atualizar_local_lote.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#mensagem').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                response.mensagem +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>');
                        } else {
                            $('#mensagem').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                response.mensagem +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>');
                        }
                        $('#atualizarModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        $('#mensagem').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            'Erro ao atualizar: ' + xhr.responseText +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>');
                    }
                });
            });

            // Lógica AJAX para o formulário de Ocorrência
            $('#ocorrenciaForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'adicionar_ocorrencia.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#mensagem').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                response.mensagem +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>');
                        } else {
                            $('#mensagem').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                response.mensagem +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>');
                        }
                        $('#ocorrenciaModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        $('#mensagem').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            'Erro ao adicionar ocorrência: ' + xhr.responseText +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>