<?php
session_start();
require 'db_connect.php';

// Obter todos os veículos do banco de dados
$nome = '';
if (isset($_GET['nome_veiculo'])) {
    $nome = mysqli_real_escape_string($conn, $_GET['nome_veiculo']);
}

$sql = "SELECT * FROM veiculos";
if ($nome != '') {
    $sql .= " WHERE nome_veiculo LIKE '%$nome%'";
}
$sql .= " ORDER BY nome_veiculo";
$result = $conn->query($sql);
$veiculo = mysqli_query($conn, $sql);

$quantidade = mysqli_num_rows($veiculo);
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <title>Lista de Veículos</title>
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>

    <style>
        /* Estilos originais preservados */
        .table-container { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .proprietario-cell { white-space: normal; line-height: 1.2; }
        .proprietario-cell p, .proprietario-cell div { margin-bottom: 0; padding: 0; }

        @media print {
            .no-print, .btn, .card-header a, form, #mensagem-php, .col-acoes { display: none !important; }
            body { font-size: 10pt; background-color: #fff; }
            h4 { text-align: center; margin: 6px auto; }
            .table-container { width: 95%; margin: 0 auto; overflow-x: visible !important; }
            table { width: 100%; font-size: 9pt; }
        }
    </style>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <div id="mensagem-php">
            <?php include('/xampp/htdocs/mensagem.php'); ?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>LISTA DE VEÍCULOS
                                <a href="cadastro_veiculos.php" class="btn btn-primary float-end no-print">
                                    <span class="bi-plus-circle-fill"></span>&nbsp;Adicionar Veículo</a>
                                <button onclick="window.print()" class="btn btn-info float-end me-2 no-print"><span class="bi-printer-fill"></span>&nbsp;Imprimir</button>
                            </h4>
                        </div>

                        <div class="card-body">
                            <form method="GET" action="lista_veiculos.php" class="no-print">
                                <div class="input-group mb-3">
                                    <input type="text" name="nome_veiculo" class="form-control" placeholder="Buscar por Nome" value="<?= htmlspecialchars($nome) ?>">
                                    <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                                </div>
                            </form>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Veículos Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;" class="col-foto">FOTO</th>
                                        <th style="text-align: center;">NOME</th>
                                        <th style="text-align: center; width:100px;">PLACA</th>
                                        <th style="text-align: center;">RENAVAN</th>
                                        <th style="text-align: center;">UF</th>
                                        <th style="text-align: center;">MARCA/MODELO</th>
                                        <th style="text-align: center;">PROPRIETÁRIO</th>
                                        <th style="text-align: center;" class="col-acoes">DOCUMENTOS</th>
                                        <th style="text-align: center;" class="col-acoes">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;" class="col-foto">
                                                    <?php if ($row['foto_veiculo']): ?>
                                                        <img src="<?= $row['foto_veiculo']; ?>" alt="Foto" style="width: 120px; height: auto;">
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['nome_veiculo']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['placa_veiculo']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['renavan_veiculo']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['uf_veiculo']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['marca_modelo_veiculo']; ?></td>
                                                <td class="proprietario-cell" style="text-align: left; vertical-align: middle;"><?= stripslashes($row['proprietario_veiculo']); ?></td>
                                                <td style="text-align: center; vertical-align: middle;" class="col-acoes">
                                                    <?php
                                                    $documentos = json_decode($row['documentos_veiculo'], true);
                                                    if (!empty($documentos) && is_array($documentos)):
                                                        foreach ($documentos as $doc):
                                                            // Lógica para nomes personalizados sem alterar o visual
                                                            $path = is_array($doc) ? $doc['path'] : $doc;
                                                            $label = is_array($doc) ? $doc['nome'] : 'Baixar';
                                                    ?>
                                                            <a href="<?= htmlspecialchars($path); ?>" target="_blank"><?= htmlspecialchars($label); ?></a><br>
                                                    <?php 
                                                        endforeach;
                                                    else: ?>
                                                        <small class="text-muted">Nenhum documento disponível.</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;" class="col-acoes">
                                                    <a href="edit_veiculos.php?id=<?= $row['cod_veiculo'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="9" style="text-align: center;">Nenhum veículo encontrado</td></tr>
                                    <?php endif; ?>
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