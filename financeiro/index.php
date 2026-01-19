<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Obter os dados dos parâmetros GET e aplicar sanitização básica
$data_inicio = isset($_GET['data_inicio']) ? htmlspecialchars($_GET['data_inicio']) : '';
$data_fim = isset($_GET['data_fim']) ? htmlspecialchars($_GET['data_fim']) : '';
$nome = isset($_GET['responsavel']) ? htmlspecialchars($_GET['responsavel']) : '';
$codigo_string = isset($_GET['cod_financeiro']) ? htmlspecialchars($_GET['cod_financeiro']) : '';

// Buscar todos os responsáveis distintos do banco de dados de forma segura
$responsaveis_query = "SELECT DISTINCT responsavel FROM financeiro ORDER BY responsavel";
$responsaveis_stmt = $conn->prepare($responsaveis_query);
$responsaveis_stmt->execute();
$responsaveis_result = $responsaveis_stmt->get_result();

$sql = "SELECT * FROM financeiro WHERE 1=1";
$tipos_parametros = '';
$parametros = [];

if ($nome != '') {
    $sql .= " AND responsavel = ? ";
    $tipos_parametros .= 's';
    $parametros[] = $nome;
}
if ($data_inicio && $data_fim) {
    $sql .= " AND data BETWEEN ? AND ?";
    $tipos_parametros .= 'ss';
    $parametros[] = $data_inicio;
    $parametros[] = $data_fim;
}

if ($codigo_string != '') {
    $codigos_array = explode(',', $codigo_string);
    $codigos_filtrados = array_map('trim', $codigos_array);
    $codigos_filtrados = array_filter($codigos_filtrados);

    if (!empty($codigos_filtrados)) {
        $placeholders = implode(',', array_fill(0, count($codigos_filtrados), '?'));
        $sql .= " AND cod_financeiro IN ($placeholders) ";
        $tipos_parametros .= str_repeat('s', count($codigos_filtrados));
        $parametros = array_merge($parametros, $codigos_filtrados);
    }
}
$sql .= " ORDER BY data LIMIT 20000";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($parametros)) {
        $stmt->bind_param($tipos_parametros, ...$parametros);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $quantidade = $result->num_rows;
} else {
    echo "Erro na preparação da consulta: " . $conn->error;
    $result = false;
    $quantidade = 0;
}

if ($result instanceof mysqli_result) {
    $financeiro = $result;
} else {
    $financeiro = false;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>Financeiro</title>

    <style>
        /* Estilos para impressão */
        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 11pt;
            }

            /* Oculta os filtros e os botões */
            .d-print-none,
            .btn {
                display: none !important;
            }

            /* Oculta as colunas de "Comprovante" e "Ações" */
            .no-print {
                display: none;
            }

            /* Limpa estilos de Bootstrap para que não interfiram */
            .container,
            .row,
            .col-md-12,
            .card {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            /* Centraliza o cabeçalho H4 */
            h4 {
                text-align: center;
                margin-bottom: 20px;
                margin: 0 auto;
            }

            /* Define o contêiner da tabela para centralizá-la */
            .table-container {
                width: 95%;
                margin: 0 auto;
                overflow-x: visible !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
                table-layout: fixed;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 5px;
                font-size: 10pt;
                word-wrap: break-word;
            }

            th {
                background-color: #f2f2f2;
                font-weight: bold;
                text-align: center;
            }

            td {
                text-align: left;
                vertical-align: top;
            }

            td img {
                width: 100px;
                height: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            /* Novas regras para larguras das colunas na impressão */
            .col-data {
                width: 15%;
            }
            .col-descricao {
                width: 35%;
            }
            .col-valor {
                width: 15%;
            }
            .col-forma-pagamento {
                width: 35%;
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
            <div class="col-md-12 offset-md-0">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>FINANCEIRO M2
                                <a href="grafico.php" class="btn btn-warning me-2 float-end"> <span class="bi bi-bar-chart"></span>&nbsp;Ver Gráfico </a>
                                <a href="importar.php" class="btn btn-success me-2 float-end"> <span class="bi bi-bar-chart"></span>&nbsp;Importar Planilha </a>
                                <button onclick="window.print()" class="btn btn-info me-2 float-end"><span class="bi bi-printer"></span>&nbsp;Imprimir</button>
                                <br>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="" class="mb-3 d-print-none">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="data_fim" class="form-label">Código:</label>
                                        <input type="text" id="" name="cod_financeiro" class="form-control" style="width:300px" value="<?= htmlspecialchars($codigo_string) ?>" placeholder="Códigos (Separar por vírgula)">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_inicio" class="form-label">Data Início:</label>
                                        <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="<?= htmlspecialchars($data_inicio) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_fim" class="form-label">Data Fim:</label>
                                        <input type="date" id="data_fim" name="data_fim" class="form-control" value="<?= htmlspecialchars($data_fim) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="responsavel" class="form-label">Responsável:</label>
                                        <select id="responsavel" name="responsavel" class="form-control">
                                            <option value="">Selecione um responsável</option>
                                            <?php while ($row = $responsaveis_result->fetch_assoc()): ?>
                                                <option value="<?= htmlspecialchars($row['responsavel']) ?>" <?= $nome == $row['responsavel'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($row['responsavel']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <br>
                                        <button type="submit" class="btn btn-primary form-control"><span class="bi bi-funnel-fill"></span>&nbsp;Filtrar</button>
                                    </div>
                                    <div class="col-md-3">
                                        <br>
                                        <a href="index.php" class="btn btn-secondary ms-2"><span class="bi-x-circle-fill"></span>&nbsp;Limpar Filtros</a>
                                    </div>
                                </div>
                            </form>

                            <div class="d-none d-print-block">
                                <h5 class="text-center">Relatório de Pagamentos</h5>
                                <?php if ($data_inicio && $data_fim): ?>
                                    <p class="text-center">Período: <?= htmlspecialchars(date('d/m/Y', strtotime($data_inicio))) ?> a <?= htmlspecialchars(date('d/m/Y', strtotime($data_fim))) ?></p>
                                <?php endif; ?>
                                <?php if ($nome): ?>
                                    <p class="text-center">Responsável: <?= htmlspecialchars($nome) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Pagamentos Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="col-data" style="text-align: center;">DATA</th>
                                        <th class="col-descricao" style="text-align: center;">DESCRIÇÃO</th>
                                        <th class="col-valor" style="text-align: center;">VALOR</th>
                                        <th class="col-forma-pagamento" style="text-align: center;">FORMA DE PAGAMENTO</th>
                                        <th class="no-print" style="text-align: center;">COMPROVANTE</th>
                                        <th class="no-print" style="text-align: center;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td class="col-data" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars(date('d/m/Y', strtotime($row['data']))) ?></td>
                                                <td class="col-descricao" style="text-align: left; vertical-align: middle;"><?= htmlspecialchars($row['descricao']); ?></td>
                                                <td class="col-valor" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($row['valor']); ?></td>
                                                <td class="col-forma-pagamento" style="text-align: left; vertical-align: middle;"><?= htmlspecialchars($row['forma_pagamento']); ?></td>
                                                <td class="no-print" style="text-align: center; vertical-align: middle;">
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
                                                <td class="no-print" style="text-align: center; vertical-align: middle;">
                                                    <a href="edit_pagamentos.php?id=<?= htmlspecialchars($row['cod_financeiro']) ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                </td>
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
                                QUANTIDADE DE PAGAMENTOS CADASTRADOS: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>