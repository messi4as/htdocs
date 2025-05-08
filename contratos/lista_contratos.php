<?php
session_start();
require 'db_connect.php';

// Obter todos os contratos do banco de dados
$nome = '';
if (isset($_GET['nome'])) {
    $nome = mysqli_real_escape_string($conn, $_GET['nome']);
}

// Obter todos os tipos de contratos do banco de dados para o filtro
$tipo_filtro = '';
if (isset($_GET['tipo_contrato'])) {
    $tipo_filtro = mysqli_real_escape_string($conn, $_GET['tipo_contrato']);
}

$sql = "SELECT id_contrato, nome, tipo_contrato, valor, status, data_inicial, data_final FROM contratos";
$conditions = [];

if ($nome != '') {
    $conditions[] = "nome LIKE '%$nome%'";
}

if ($tipo_filtro != '') {
    $conditions[] = "tipo_contrato LIKE '%$tipo_filtro%'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY data_final ASC";
$result = $conn->query($sql);
$contrato = mysqli_query($conn, $sql);

$quantidade = mysqli_num_rows($contrato);

// Função para obter opções únicas de uma coluna
function getOptions($conn, $column)
{
    $options = [];
    $sql = "SELECT DISTINCT $column FROM contratos ORDER BY $column ASC";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row[$column];
    }
    return $options;
}

$tipoOptions = getOptions($conn, 'tipo_contrato');

?>

<!doctype html>
<html lang="pt-br">

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

    .vencimento-proximo {
        color: orange;
        font-weight: bold;
    }

    .vencido {
        color: red;
        font-weight: bold;
    }

    .vigente {
        color: rgba(207, 244, 252, 0.5);
        font-weight: bold;
    }

    .progress-bar-container {
        width: 150px;
        /* Aumentei a largura */
        height: 15px;
        /* Aumentei a altura */
        background-color: #f0f0f0;
        border-radius: 5px;
        overflow: hidden;
        margin: 0 auto;
        /* Centraliza a barra */
    }

    .progress-bar {
        height: 100%;
        background-color: rgba(11, 62, 73, 0.5);
        /* Cor padrão para contratos vigentes */
        width: 0%;
        /* Largura inicial */
        border-radius: 10px;
    }

    .vencimento-proximo .progress-bar {
        background-color: orange;
    }

    .vencido .progress-bar {
        background-color: red;
    }

    .table td.vencimento-proximo .progress-bar {
        background-color: rgba(199, 123, 24, 0.6) !important;
        /* O !important pode forçar a aplicação, mas use com cautela */
    }

    .table td.vencido .progress-bar {
        background-color: red !important;
    }

    .table td.vigente .progress-bar {
        background-color: rgba(47, 109, 39, 0.6) !important;
    }
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <title>Lista de Contratos</title>
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <?php include('/xampp/htdocs/mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>LISTA DE CONTRATOS REGISTRADOS
                                <a href="cadastro_contratos.php" class="btn btn-primary float-end"><span class="bi-plus-circle-fill"></span>&nbsp;Adicionar Contrato</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="lista_contratos.php">
                                <div class="input-group mb-3">
                                    <input type="text" name="nome" class="form-control" placeholder="Buscar por Nome" value="<?= $nome ?>">
                                    <select name="tipo_contrato" class="form-control me-2">
                                        <option value="">Filtrar por Tipo</option>
                                        <?php foreach ($tipoOptions as $option) : ?>
                                            <option value="<?= $option ?>" <?= $tipo_filtro == $option ? 'selected' : '' ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Filtrar</button>
                                    <a href="lista_contratos.php" class="btn btn-secondary ms-2"><span class="bi-x-circle-fill"></span>&nbsp;Limpar Filtros</a>
                                </div>
                            </form>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Contratos Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;">VENCIMENTO</th>
                                        <th style="text-align: center; vertical-align: middle;">NOME</th>
                                        <th style="text-align: center; vertical-align: middle;">TIPO</th>
                                        <th style="text-align: center; vertical-align: middle;">VALOR</th>
                                        <th style="text-align: center; vertical-align: middle;">STATUS</th>
                                        <th style="text-align: center; vertical-align: middle;">DATA DE INÍCIO</th>
                                        <th style="text-align: center; vertical-align: middle;">DATA DE VENCIMENTO</th>
                                        <th style="text-align: center; vertical-align: middle;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $dataInicial = new DateTime($row['data_inicial']);
                                            $dataFinal = new DateTime($row['data_final']);
                                            $dataAtual = new DateTime();
                                            $intervaloTotal = $dataInicial->diff($dataFinal);
                                            $intervaloRestante = $dataAtual->diff($dataFinal);

                                            $diasTotal = $intervaloTotal->days;
                                            $diasRestantes = $intervaloRestante->days;

                                            $tempoRestante = '';
                                            $classeVencimento = '';
                                            $porcentagemRestante = 0; // Inicializa com 0

                                            if ($dataFinal >= $dataAtual) {
                                                if ($diasTotal > 0) {
                                                    $porcentagemRestante = ($diasRestantes / $diasTotal) * 100;
                                                    $porcentagemRestante = max(0, min(100, $porcentagemRestante));
                                                } else {
                                                    $porcentagemRestante = 100; // Se data inicial e final são iguais, considera 100%
                                                }

                                                if ($intervaloRestante->y > 0) {
                                                    $tempoRestante .= $intervaloRestante->y . ' ano(s) ';
                                                }
                                                if ($intervaloRestante->m > 0) {
                                                    $tempoRestante .= $intervaloRestante->m . ' mês(es) ';
                                                }
                                                if ($intervaloRestante->d > 0) {
                                                    $tempoRestante .= $intervaloRestante->d . ' dia(s)';
                                                }
                                                if (empty($tempoRestante)) {
                                                    $tempoRestante = 'Hoje';
                                                } else if ($intervaloRestante->days <= 30) {
                                                    $classeVencimento = 'vencimento-proximo';
                                                } else {
                                                    $classeVencimento = 'vigente';
                                                }
                                            } else {
                                                $tempoRestante = 'Vencido';
                                                $classeVencimento = 'vencido';
                                                $porcentagemRestante = 0;
                                            }
                                    ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;" class="<?= $classeVencimento ?>">
                                                    <div class="progress-bar-container">
                                                        <div class="progress-bar" style="width: <?= $porcentagemRestante ?>%;"></div>
                                                    </div>
                                                    <small><?= $tempoRestante; ?></small>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['nome']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['tipo_contrato']; ?></td>
                                                <td style="text-align: center; vertical-align: middle; width:150px"><?= $row['valor']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['status']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($row['data_inicial'])); ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($row['data_final'])); ?></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <a href="edit_contratos.php?id=<?= $row['id_contrato'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" style="text-align: center;">Nenhum Contrato encontrado</td></tr>';
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
</body>

</html>