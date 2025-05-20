<?php
session_start();
require 'db_connect.php';

// Recuperar locais
$locais_result = mysqli_query($conn, "SELECT DISTINCT local FROM bovinos where status = 'ATIVO'");
$locais = mysqli_fetch_all($locais_result, MYSQLI_ASSOC);

// Recuperar sexos
$sexos_result = mysqli_query($conn, "SELECT DISTINCT sexo FROM bovinos");
$sexos = mysqli_fetch_all($sexos_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>NASCIMENTOS</title>
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
                            <h4>NASCIMENTOS <div class="float-end">
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

                                    <select name="sexo" class="form-control" style="max-width: 200px;">
                                        <option value="">Selecione o Sexo</option>
                                        <?php foreach ($sexos as $sexo): ?>
                                            <option value="<?= htmlspecialchars($sexo['sexo']) ?>" <?= isset($_GET['sexo']) && $_GET['sexo'] == $sexo['sexo'] ? 'selected' : '' ?>><?= htmlspecialchars($sexo['sexo']) ?></option>
                                        <?php endforeach; ?>
                                    </select> &nbsp; &nbsp;

                                    <button class="btn btn-primary" type="submit" style="max-width: 100px;">Filtrar</button>
                                </div>
                            </form>
                            <?php
                            $data_inicial = isset($_GET['data_inicial']) ? $_GET['data_inicial'] : '';
                            $data_final = isset($_GET['data_final']) ? $_GET['data_final'] : '';
                            $local = isset($_GET['local']) ? $_GET['local'] : '';
                            $sexo = isset($_GET['sexo']) ? $_GET['sexo'] : '';

                            $sql = "SELECT n.id, b.brinco AS brinco_bezerro, 
               TIMESTAMPDIFF(MONTH, b.data_nascimento, CURDATE()) AS idade, 
               m.brinco AS brinco_mae, 
               b.local, 
               b.lote 
        FROM nascimentos n
        JOIN bovinos b ON n.cod_bezerro = b.cod_animal
        JOIN bovinos m ON n.cod_mae = m.cod_animal";

                            $conditions = [];
                            if ($data_inicial != '' && $data_final != '') {
                                $conditions[] = "n.data_nascimento BETWEEN '$data_inicial' AND '$data_final'";
                            }
                            if ($local != '') {
                                $conditions[] = "b.local = '$local'";
                            }
                            if ($sexo != '') {
                                $conditions[] = "b.sexo = '$sexo'";
                            }

                            if (count($conditions) > 0) {
                                $sql .= " WHERE " . implode(' AND ', $conditions);
                            }

                            $sql .= " ORDER BY b.brinco DESC";

                            $result = mysqli_query($conn, $sql);
                            $quantidade = mysqli_num_rows($result);
                            ?>
                            <div class="alert alert-info" role="alert">
                                Quantidade de Nascimentos: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style='text-align: center; vertical-align: middle;'>BRINCO DO BEZERRO</th>
                                        <th style='text-align: center; vertical-align: middle;'>IDADE (MESES)</th>
                                        <th style='text-align: center; vertical-align: middle;'>BRINCO DA MÃE</th>
                                        <th style='text-align: center; vertical-align: middle;'>LOCAL</th>
                                        <th style='text-align: center; vertical-align: middle;'>LOTE</th>
                                        <th style='text-align: center; vertical-align: middle;'>AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($quantidade > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['brinco_bezerro'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['idade'] . " meses</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['brinco_mae'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['local'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['lote'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>
                                <a href='edit_nascimento.php?id=" . $row['id'] . "' class='btn btn-secondary btn-sm'><span class='bi-pencil'></span>&nbsp;Editar</a>
                              </td>";
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