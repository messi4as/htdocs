<?php
session_start();
require 'db_connect.php';

// Recuperar os status do banco de dados em ordem alfabética
$status_query = "SELECT DISTINCT status FROM bovinos_com_idade ORDER BY status ASC";
$status_result = mysqli_query($conn, $status_query);

// Recuperar os lotes do banco de dados em ordem alfabética
$lote_query = "SELECT DISTINCT lote FROM bovinos_com_idade ORDER BY lote ASC";
$lote_result = mysqli_query($conn, $lote_query);

// RECUPERAR OS PASTOS DO BANCO DE DADOS em ordem alfabética
$pasto_query = "SELECT DISTINCT pasto FROM bovinos_com_idade ORDER BY pasto ASC";
$pasto_result = mysqli_query($conn, $pasto_query);

?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>GESTÃO DE BOVINOS</title>

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
    </style>

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
                            <h4>BOVINOS - FAZENDA ROSADA
                                <div class="float-end">
                                    <a href="estratificacao_img.php" class="btn btn-info me-2 float-end"> <span class="bi bi-image"></span>&nbsp;Ver Imagens </a>
                                    <a href="cad_nascimento.php" class="btn btn-success"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Novo Nascimento</a>
                                    <a href="cad_animal.php" class="btn btn-primary me-2"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Novo Animal</a>
                                    <a href="visualizacao_graficos.php" class="btn btn-warning me-2 float-end"> <span class="bi bi-bar-chart"></span>&nbsp;Ver Gráfico por Grupo </a>
                                </div>
                            </h4>
                        </div>
                        <div class="card-body">

                            <form method="GET" action="" class="mb-1">
                                <div class="input-group mb-3">
                                    <input type="number" name="cod_animal" class="form-control" placeholder="Cód. Animal" value="<?= htmlspecialchars($_GET['cod_animal'] ?? '') ?>"> &nbsp; &nbsp;
                                    <input type="text" name="brinco" class="form-control" placeholder="Buscar por Brinco (separados por vírgula)" value="<?= htmlspecialchars($_GET['brinco'] ?? '') ?>"> &nbsp; &nbsp;
                                    <select name="status" class="form-control">
                                        <option value="">Buscar por status</option>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($status_result)) {
                                            $selected = (isset($_GET['status']) && $_GET['status'] == $row['status']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['status']) . '" ' . $selected . '>' . htmlspecialchars($row['status']) . '</option>';
                                        }
                                        ?>
                                    </select> &nbsp; &nbsp;
                                    <select name="lote" class="form-control">
                                        <option value="">Buscar por lote</option>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($lote_result)) {
                                            $selected = (isset($_GET['lote']) && $_GET['lote'] == $row['lote']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['lote']) . '" ' . $selected . '>' . htmlspecialchars($row['lote']) . '</option>';
                                        }
                                        ?>
                                    </select> &nbsp; &nbsp;
                                    <select name="pasto" class="form-control">
                                        <option value="">Buscar por Pasto</option>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($pasto_result)) {
                                            $selected = (isset($_GET['pasto']) && $_GET['pasto'] == $row['pasto']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['pasto']) . '" ' . $selected . '>' . htmlspecialchars($row['pasto']) . '</option>';
                                        }
                                        ?>
                                    </select> &nbsp; &nbsp;
                                    <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                                </div>
                            </form>

                            <?php
                            $brinco = mysqli_real_escape_string($conn, $_GET['brinco'] ?? '');
                            $status = mysqli_real_escape_string($conn, $_GET['status'] ?? '');
                            $lote = mysqli_real_escape_string($conn, $_GET['lote'] ?? '');
                            $pasto = mysqli_real_escape_string($conn, $_GET['pasto'] ?? '');
                            $cod_animal_busca = mysqli_real_escape_string($conn, $_GET['cod_animal'] ?? '');

                            $sql = "SELECT * FROM bovinos_com_idade WHERE 1=1";
                            
                            if ($cod_animal_busca != '') {
                                $sql .= " AND cod_animal = '$cod_animal_busca'";
                            }
                            if ($brinco != '') {
                                $brincos = explode(',', $brinco);
                                $brincos = array_map('trim', $brincos);
                                $brinco_conditions = array();
                                foreach ($brincos as $b) {
                                    $brinco_conditions[] = "brinco LIKE '%" . mysqli_real_escape_string($conn, $b) . "%'";
                                }
                                $sql .= " AND (" . implode(' OR ', $brinco_conditions) . ")";
                            }
                            if ($status != '') {
                                $sql .= " AND status LIKE '%$status%'";
                            }
                            if ($lote != '') {
                                $sql .= " AND lote LIKE '%$lote%'";
                            }
                            if ($pasto != '') {
                                $sql .= " AND pasto LIKE '%$pasto%'";
                            }
                            $sql .= " ORDER BY cod_animal DESC"; 
                            
                            $bovino = mysqli_query($conn, $sql);
                            $quantidade = mysqli_num_rows($bovino);
                            ?>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Bovinos Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">CÓDIGO</th>
                                        <th style="text-align: center;">BRINCO</th>
                                        <th style="text-align: center;">IDADE (MESES)</th>
                                        <th style="text-align: center;">LOCAL</th>
                                        <th style="text-align: center;">LOTE</th>
                                        <th style="text-align: center;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($quantidade > 0) {
                                        foreach ($bovino as $bovinos) {
                                            $idade = $bovinos['idade'];
                                            $idade_texto = $idade . ' ' . ($idade == 1 ? 'mês' : 'meses');
                                            $codigo_exibicao = str_pad($bovinos['cod_animal'], 4, '0', STR_PAD_LEFT);
                                    ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;"><b><?= $codigo_exibicao ?></b></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($bovinos['brinco']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($idade_texto) ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($bovinos['local']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($bovinos['lote']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <a href="view_animal.php?id=<?= htmlspecialchars($bovinos['cod_animal']) ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center"><h5>Nenhum Animal Encontrado</h5></td></tr>';
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