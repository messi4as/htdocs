<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Obter os dados dos parâmetros GET
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$nome = isset($_GET['responsavel']) ? $_GET['responsavel'] : '';
$codigo_string = isset($_GET['cod_financeiro']) ? $_GET['cod_financeiro'] : '';

// Buscar todos os responsáveis distintos do banco de dados
$responsaveis_query = "SELECT DISTINCT responsavel FROM financeiro";
$responsaveis_result = $conn->query($responsaveis_query);

$sql = "SELECT * FROM financeiro WHERE 1=1"; // Para facilitar a inclusão das condições
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
$sql .= " ORDER BY data";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($parametros)) {
        $stmt->bind_param($tipos_parametros, ...$parametros);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $quantidade = $result->num_rows;
} else {
    // Lidar com erro na preparação da consulta
    echo "Erro na preparação da consulta: " . $conn->error;
    $result = false;
    $quantidade = 0;
}

// Para compatibilidade com o seu código existente, você pode criar um objeto mysqli_result
// simulado se $result for um objeto mysqli_result
if ($result instanceof mysqli_result) {
    $financeiro = $result;
} else {
    $financeiro = false; // Ou outra forma de indicar que não há resultados
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
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
        }

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

                             
                            <a href="grafico.php" class="btn btn-warning me-2 float-end"> <span class="bi bi-bar-chart"></span>&nbsp;Ver Gráfico </a>
                            <a href="importar.php" class="btn btn-success me-2 float-end"> <span class="bi bi-bar-chart"></span>&nbsp;Importar Planilha </a>   
                                <!-- <a href="cad_pagamentos.php" class="btn btn-success me-2 float-end "> <span class="bi-plus-circle-fill"></span>&nbsp;Adicionar </a>-->

                                <br>

                            </h4>


                            <!-- //Importar Excel Financeiro 
                            
                                        <form action="importar_excel_financeiro.php" method="post" enctype="multipart/form-data">                                         
                                            <label class="form-label">Importar Planilha Excel:</label>
                                        <div class="row">  
                                        
                                        <div class="col-md-5">                                      
                                            <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="submit" name="importar_excel_financeiro" class="btn btn-warning me-2 form-control">Importar Financeiro</button>
                                        </div>                                        
                                        </form>   
                            -->


                               <!-- //Atualizar Dados Fianceiros

                                        <form action="atualizar_financeiro.php" method="post" enctype="multipart/form-data">
                                            <label class="form-label">Importar Planilha Excel:</label>
                                        <div class="row">

                                        <div class="col-md-5">
                                            <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="submit" name="atualizar_financeiro" class="btn btn-success form-control">Atualizar Financeiro</button>
                                        </div>
                                        </form>

        -->


                            <!-- //Atualizar Dados Fianceiros

                                        <form action="atualizar_documentos.php" method="post" enctype="multipart/form-data">
                                            <label class="form-label">Importar Planilha Excel:</label>
                                        <div class="row">

                                        <div class="col-md-5">
                                            <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="submit" name="atualizar_documentos" class="btn btn-success form-control">Atualizar Documentos</button>
                                        </div>
                                        </form>

                            -->



                            <div class="row">

                            </div>

                        </div>



                        <div class="card-body">
                            <!-- Filtros -->
                            <form method="GET" action="" class="mb-3">


                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="data_fim" class="form-label">Código:</label>
                                        <input type="text" id="" name="cod_financeiro" class="form-control" style="width:300px" value="<?= $codigo_string ?>" placeholder="Códigos (Separar por vírgula)">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_inicio" class="form-label">Data Início:</label>
                                        <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="<?= $data_inicio ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_fim" class="form-label">Data Fim:</label>
                                        <input type="date" id="data_fim" name="data_fim" class="form-control" value="<?= $data_fim ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="responsavel" class="form-label">Responsável:</label>
                                        <select id="responsavel" name="responsavel" class="form-control">
                                            <option value="">Selecione um responsável</option>
                                            <?php while ($row = $responsaveis_result->fetch_assoc()): ?>
                                                <option value="<?= $row['responsavel'] ?>" <?= $nome == $row['responsavel'] ? 'selected' : '' ?>>
                                                    <?= $row['responsavel'] ?>
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


                            <div class="alert alert-info" role="alert">
                                Quantidade de Pagamentos Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">DATA</th>
                                        <th style="text-align: center; width:450px">DESCRIÇÃO</th>
                                        <th style="text-align: center; width:150px">VALOR</th>
                                        <th style="text-align: center; width:450px">FORMA DE PAGAMENTO</th>

                                        <th style="text-align: center;">COMPROVANTE</th>
                                        <th style="text-align: center;">AÇÕES</th>
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