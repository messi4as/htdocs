<?php
session_start();
require 'db_connect.php';

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
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <title>NOVO NASCIMENTO</title>

    <style>
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1 1 45%;
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

        table {
            width: 100%;
            border-collapse: collapse;
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
        <?php include('mensagem.php'); ?>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>CADASTRO DE NASCIMENTO
                                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="registrar_nascimento.php" method="POST" enctype="multipart/form-data">
                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="brinco_bezerro" class="form-label">BRINCO DO BEZERRO:</label>
                                        <input type="text" name="brinco_bezerro" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="brinco_mae" class="form-label">BRINCO DA MÂE:</label>
                                        <select id="brinco_mae" name="brinco_mae" class="form-control" required>
                                            <?php foreach ($brincos_maes as $brinco): ?>
                                                <option value="<?= $brinco['brinco']; ?>"><?= $brinco['brinco']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="data_nascimento" class="form-label">DATA DE NASCIMENTO:</label>
                                        <input type="date" name="data_nascimento" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="sexo_bezerro" class="form-label">SEXO:</label>
                                        <select name="sexo_bezerro" class="form-control" required>
                                            <option value="" disabled selected>Selecione o Sexo</option>
                                            <option value="MACHO">MACHO</option>
                                            <option value="FÊMEA">FÊMEA</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="agrupamento" class="form-label">GRUPO:</label>
                                        <select name="agrupamento" class="form-control" required>
                                            <option value="" disabled selected>Selecione o Grupo</option>
                                            <option value="BEZERRA">BEZERRA</option>
                                            <option value="BEZERRA DE LEITE">BEZERRA DE LEITE</option>
                                            <option value="BEZERRO">BEZERRO</option>
                                            <option value="BEZERRO DE LEITE">BEZERRO DE LEITE</option>
                                            <option value="GARROTE">GARROTE</option>
                                            <option value="GARROTE DE LEITE">GARROTE DE LEITE</option>
                                            <option value="NOVILHA">NOVILHA</option>
                                            <option value="NOVILHA DE LEITE">NOVILHA DE LEITE</option>
                                            <option value="TOURO">TOURO</option>
                                            <option value="VACA">VACA</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="situacao_atual" class="form-label">SITUAÇÃO ATUAL:</label>
                                        <input type="text" name="situacao_atual" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="estratificacao" class="form-label">ESTRATIFICAÇÃO:</label>
                                        <select name="estratificacao" class="form-control" required>
                                            <option value="Macho, 0 a 12 meses">Macho, 0 a 12 meses</option>
                                            <option value="Fêmea, 0 a 12 meses">Fêmea, 0 a 12 meses</option>
                                            <option value="Macho, 13 a 24 meses">Macho, 13 a 24 meses</option>
                                            <option value="Fêmea, 13 a 24 meses">Fêmea, 13 a 24 meses</option>
                                            <option value="Macho, 25 a 36 meses">Macho, 25 a 36 meses</option>
                                            <option value="Fêmea, 25 a 36 meses">Fêmea, 25 a 36 meses</option>
                                            <option value="Macho, Acima de 36 meses">Macho, Acima de 36 meses</option>
                                            <option value="Fêmea, Acima de 36 meses">Fêmea, Acima de 36 meses</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success"><span class="bi-save"></span>&nbsp;Registrar Nascimento</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>