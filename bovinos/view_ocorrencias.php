<?php
session_start();
require 'db_connect.php';

// Recuperar locais
$locais_result = mysqli_query($conn, "SELECT DISTINCT local FROM ocorrencias where cod_animal in (SELECT cod_animal FROM bovinos)");
$locais = mysqli_fetch_all($locais_result, MYSQLI_ASSOC);

// Recuperar Tipos
$tipos_result = mysqli_query($conn, "SELECT DISTINCT tipo FROM ocorrencias where cod_animal in (SELECT cod_animal FROM bovinos)");
$tipos = mysqli_fetch_all($tipos_result, MYSQLI_ASSOC);

// Recuperar Brincos
$brincos_result = mysqli_query($conn, "SELECT DISTINCT brinco FROM bovinos where cod_animal in (SELECT cod_animal FROM ocorrencias)");
$brincos = mysqli_fetch_all($brincos_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>OCORRÊNCIAS</title>
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
                            <h4>REGISTROS DE OCORRÊNCIAS <div class="float-end">
                            </h4>
                        </div>
                        <div class="card-body">

                            <form method="GET" action="">
                                <div class="input-group mb-3">
                                    <label for="data_inicial" style="text-align: left; vertical-align: middle;"><strong>FILTRO POR DATA: &nbsp;</strong></label>
                                    <input type="date" id="data_inicial" name="data_inicial" class="form-control" placeholder="Data Inicial" style="max-width: 200px;" value="<?= isset($_GET['data_inicial']) ? htmlspecialchars($_GET['data_inicial']) : '' ?>"> &nbsp; &nbsp;
                                    <input type="date" id="data_final" name="data_final" class="form-control" placeholder="Data Final" style="max-width: 200px;" value="<?= isset($_GET['data_final']) ? htmlspecialchars($_GET['data_final']) : '' ?>"> &nbsp; &nbsp;

                                    <select name="local" class="form-control" style="max-width: 300px;">
                                        <option value="">Selecione o Local</option>
                                        <?php foreach ($locais as $local): ?>
                                            <option value="<?= htmlspecialchars($local['local']) ?>" <?= isset($_GET['local']) && $_GET['local'] == $local['local'] ? 'selected' : '' ?>><?= htmlspecialchars($local['local']) ?></option>
                                        <?php endforeach; ?>
                                    </select> &nbsp; &nbsp;

                                    <select name="tipo" class="form-control" style="max-width: 200px;">
                                        <option value="">Selecione o Tipo</option>
                                        <?php foreach ($tipos as $tipo): ?>
                                            <option value="<?= htmlspecialchars($tipo['tipo']) ?>" <?= isset($_GET['tipo']) && $_GET['tipo'] == $tipo['tipo'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['tipo']) ?></option>
                                        <?php endforeach; ?>
                                    </select> &nbsp; &nbsp;

                                    <select name="brinco" class="form-control" style="max-width: 200px;">
                                        <option value="">Selecione o Brinco</option>
                                        <?php foreach ($brincos as $brinco): ?>
                                            <option value="<?= htmlspecialchars($brinco['brinco']) ?>" <?= isset($_GET['brinco']) && $_GET['brinco'] == $brinco['brinco'] ? 'selected' : '' ?>><?= htmlspecialchars($brinco['brinco']) ?></option>
                                        <?php endforeach; ?>
                                    </select> &nbsp; &nbsp;

                                    <button class="btn btn-primary" type="submit" style="max-width: 100px;">Filtrar</button>
                                </div>
                            </form>
                            <?php
$data_inicial = isset($_GET['data_inicial']) ? $_GET['data_inicial'] : '';
$data_final = isset($_GET['data_final']) ? $_GET['data_final'] : '';
$local = isset($_GET['local']) ? $_GET['local'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$brinco = isset($_GET['brinco']) ? $_GET['brinco'] : '';

// Use declarações preparadas para prevenir injeção de SQL
$sql = "SELECT
            o.id,
            o.data,  -- Coluna 'data' corrigida
            o.descricao,
            b.brinco AS brinco,
            b.cod_animal,
            TIMESTAMPDIFF(MONTH, b.data_nascimento, o.data) AS idade,
            b.local,
            o.peso
        FROM
            ocorrencias o
        INNER JOIN
            bovinos b ON o.cod_animal = b.cod_animal
        WHERE 1=1 "; // Comece com uma condição que é sempre verdadeira

$conditions = [];
if ($data_inicial != '' && $data_final != '') {
    $conditions[] = "o.data BETWEEN ? AND ?"; // Use marcadores de posição
}
if ($local != '') {
    $conditions[] = "b.local = ?"; // Use marcadores de posição
}
if ($tipo != '') {
    $conditions[] = "o.tipo = ?"; // Use marcadores de posição
}
if ($brinco != '') {
    $conditions[] = "b.brinco = ?"; // Use marcadores de posição
}


if (count($conditions) > 0) {
    $sql .= " AND " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY o.data asc"; // Ordenar por data ascendente

$stmt = mysqli_prepare($conn, $sql); // Prepare a declaração

if ($stmt) 
    // Vincule os parâmetros
    $types = '';
    $params = [];
    if ($data_inicial != '' && $data_final != '') {
        $types .= 'ss'; // String, String
        $params[] =  $data_inicial;
        $params[] =  $data_final;
    }
    if ($local != '') {
        $types .= 's'; // String
        $params[] =  $local;
    }
    if ($tipo != '') {
        $types .= 's'; // String
        $params[] =  $tipo;
    }
    if ($brinco != '') {
        $types .= 's'; // String
        $params[] =  $brinco;
    }
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Execute a declaração
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $quantidade = mysqli_num_rows($result);

?>


                            <div class="alert alert-info" role="alert">
                                Quantidade de Ocorrências: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style='text-align: center; vertical-align: middle;'>DATA</th>
                                        <th style='text-align: center; vertical-align: middle; width: 150px;'>IDADE (MESES)</th>
                                        <th style='text-align: center; vertical-align: middle;'>BRINCO</th>
                                        <th style='text-align: center; vertical-align: middle;'>PESO</th>
                                        <th style='text-align: center; vertical-align: middle; width: 250px;'>LOCAL</th>
                                        <th style='text-align: center; vertical-align: middle;'>DESCRIÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($quantidade > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['idade'] . " meses</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'> <a href='view_animal.php?id=" . urlencode($row['cod_animal']) . "' style='text-decoration: none;'>" . htmlspecialchars($row['brinco']) . "</a></td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['peso'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['local'] . "</td>";
                                            echo "<td style='text-align: left; vertical-align: middle;'>" . $row['descricao'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>Nenhum nascimento encontrado</td></tr>";
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



    <script>
        // Ocultar a mensagem após 5 segundos
        setTimeout(function() {
            var mensagemAlert = document.getElementById('mensagem-alert');
            if (mensagemAlert) {
                mensagemAlert.classList.remove('show');
                mensagemAlert.classList.add('fade');
                setTimeout(function() {
                    mensagemAlert.remove();
                }, 500); // Tempo para a transição de fade
            }
        }, 5000);
    </script>
</body>

</html>