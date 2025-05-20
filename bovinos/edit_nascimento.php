<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT n.*, b1.brinco AS brinco_bezerro, b2.brinco AS brinco_mae, b1.data_nascimento, b1.sexo AS sexo_bezerro, b1.agrupamento, b1.situacao_atual
              FROM nascimentos n
              JOIN bovinos b1 ON n.cod_bezerro = b1.cod_animal
              JOIN bovinos b2 ON n.cod_mae = b2.cod_animal
              WHERE n.id = $id";
    $result = mysqli_query($conn, $query);
    $nascimento = mysqli_fetch_assoc($result);
}

// Buscar brincos de fêmeas e agrupamento "vaca"
$query_maes = "SELECT brinco FROM bovinos WHERE sexo = 'FÊMEA' AND agrupamento like 'VACA%'";
$result_maes = mysqli_query($conn, $query_maes);
$brincos_maes = mysqli_fetch_all($result_maes, MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <title>EDITAR NASCIMENTO</title>
    <style>
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
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
    <script>
        $(document).ready(function() {
            $('#brinco_mae').select2({
                placeholder: 'Selecione um brinco',
                allowClear: true
            });
        });
    </script>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>EDITAR NASCIMENTO
                            <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="atualizar_nascimento.php" method="POST">
                            <input type="hidden" name="id" value="<?= isset($nascimento['id']) ? $nascimento['id'] : ''; ?>">
                            <div class="form-container">
                                <div class="form-group">
                                    <label for="brinco_bezerro" class="form-label">BRINCO DO BEZERRO:</label>
                                    <input type="text" name="brinco_bezerro" class="form-control" value="<?= isset($nascimento['brinco_bezerro']) ? $nascimento['brinco_bezerro'] : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="brinco_mae" class="form-label">BRINCO DA MÃE:</label>
                                    <select id="brinco_mae" name="brinco_mae" class="form-control" required>
                                        <?php foreach ($brincos_maes as $brinco): ?>
                                            <option value="<?= $brinco['brinco']; ?>" <?= $brinco['brinco'] == $nascimento['brinco_mae'] ? 'selected' : ''; ?>><?= $brinco['brinco']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="data_nascimento" class="form-label">DATA DE NASCIMENTO:</label>
                                    <input type="date" name="data_nascimento" class="form-control" value="<?= isset($nascimento['data_nascimento']) ? $nascimento['data_nascimento'] : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="sexo_bezerro" class="form-label">SEXO:</label>
                                    <select name="sexo_bezerro" class="form-control" required>
                                        <option value="" disabled <?= !isset($nascimento['sexo_bezerro']) ? 'selected' : ''; ?>>Selecione o Sexo</option>
                                        <option value="MACHO" <?= isset($nascimento['sexo_bezerro']) && $nascimento['sexo_bezerro'] == 'MACHO' ? 'selected' : ''; ?>>MACHO</option>
                                        <option value="FÊMEA" <?= isset($nascimento['sexo_bezerro']) && $nascimento['sexo_bezerro'] == 'FÊMEA' ? 'selected' : ''; ?>>FÊMEA</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="agrupamento" class="form-label">GRUPO:</label>
                                    <select name="agrupamento" class="form-control" required>
                                        <option value="" disabled <?= !isset($nascimento['agrupamento']) ? 'selected' : ''; ?>>Selecione o Grupo</option>
                                        <option value="BEZERRA" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'BEZERRA' ? 'selected' : ''; ?>>BEZERRA</option>
                                        <option value="BEZERRA DE LEITE" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'BEZERRA DE LEITE' ? 'selected' : ''; ?>>BEZERRA DE LEITE</option>
                                        <option value="BEZERRO" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'BEZERRO' ? 'selected' : ''; ?>>BEZERRO</option>
                                        <option value="BEZERRO DE LEITE" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'BEZERRO DE LEITE' ? 'selected' : ''; ?>>BEZERRO DE LEITE</option>
                                        <option value="GARROTE" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'GARROTE' ? 'selected' : ''; ?>>GARROTE</option>
                                        <option value="GARROTE DE LEITE" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'GARROTE DE LEITE' ? 'selected' : ''; ?>>GARROTE DE LEITE</option>
                                        <option value="NOVILHA" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'NOVILHA' ? 'selected' : ''; ?>>NOVILHA</option>
                                        <option value="NOVILHA DE LEITE" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'NOVILHA DE LEITE' ? 'selected' : ''; ?>>NOVILHA DE LEITE</option>
                                        <option value="TOURO" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'TOURO' ? 'selected' : ''; ?>>TOURO</option>
                                        <option value="VACA" <?= isset($nascimento['agrupamento']) && $nascimento['agrupamento'] == 'VACA' ? 'selected' : ''; ?>>VACA</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="situacao_atual" class="form-label">SITUAÇÃO ATUAL:</label>
                                    <input type="text" name="situacao_atual" class="form-control" value="<?= isset($nascimento['situacao_atual']) ? $nascimento['situacao_atual'] : ''; ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success"><span class="bi-save"></span>&nbsp;Atualizar Nascimento</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>