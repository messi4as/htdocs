<?php
session_start();
require 'db_connect.php';


// Obter todos os itens do banco de dados
$nome = '';
if (isset($_GET['nome_item'])) {
    $nome = mysqli_real_escape_string($conn, $_GET['nome_item']);
}

// Obter o local a partir da URL
$local = '';
if (isset($_GET['local'])) {
    $local = mysqli_real_escape_string($conn, $_GET['local']);
}

$sql = "SELECT * FROM galpao";
if ($nome != '') {
    $sql .= " WHERE nome_item LIKE '%$nome%'";
}
if ($local != '') {
    $sql .= $nome != '' ? " AND local LIKE '%$local%'" : " WHERE local LIKE '%$local%'";
}
$sql .= " ORDER BY nome_item";
$result = $conn->query($sql);
$item = mysqli_query($conn, $sql);

$quantidade = mysqli_num_rows($item);


// Função para obter opções únicas de uma coluna
function getOptions($conn, $column)
{
    $options = [];
    $sql = "SELECT DISTINCT $column FROM galpao";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row[$column];
    }
    return $options;
}

$localOptions = getOptions($conn, 'local');
// Função para obter dados dos bovinos com filtros
function getBovinosData($conn, $local)
{
    $sql = "SELECT local COUNT(*) as quantidade 
            FROM galpao 
            WHERE 1=1";
    if ($local != '') {
        $sql .= " AND local LIKE '%$local%'";
    }
    $conditions = [];
    if ($local != '') {
        $conditions[] = "local LIKE '%$local%'";
    }

    $sql .= " GROUP BY local";
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}


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
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>

    <title>Galpão</title>

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

                            <h4>LISTA DE ITENS ARMAZENADOS NO GALPÃO
                                <a href="cadastro_item.php" class="btn btn-primary float-end"><span class="bi-plus-circle-fill"></span>&nbsp;Adicionar Item</a>

                            </h4>
                        </div>

                        <div class="card-body">
                            <form method="GET" action="lista_item.php">
                                <div class="input-group mb-3">
                                    <input type="text" name="nome_item" class="form-control" placeholder="Buscar por Nome">
                                    <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                                </div>
                            </form>

                            <form method="GET" action="lista_item.php" class="d-flex align-items-center">
                                <select name="local" class="form-control me-2">
                                    <option value="">Local</option>
                                    <?php foreach ($localOptions as $option) : ?>
                                        <option value="<?= $option ?>" <?= $local == $option ? 'selected' : '' ?>><?= $option ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-primary" type="submit">Filtrar</button>
                            </form>
                            <br>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Itens Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">FOTO</th>
                                        <th style="text-align: center;">NOME</th>
                                        <th style="text-align: center;">CATEGORIA</th>
                                        <th style="text-align: center;">ORIGEM</th>
                                        <th style="text-align: center;">QUANTIDADE</th>
                                        <th style="text-align: center;">DATA DE ENTRADA</th>
                                        <th style="text-align: center;">LOCAL</th>

                                        <th style="text-align: center;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <?php if ($row['foto']): ?>
                                                        <img src="<?= $row['foto']; ?>" alt="Foto do Item" style="width: 100px; height: auto;">
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['nome_item']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['categoria']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['origem']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['quantidade']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($row['data_entrada'])); ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= ($row['local']); ?></td>
                                                <!-- <td style="text-align: center; vertical-align: middle;">
                                                    <?php
                                                    $documentos = json_decode($row['anexo_documento'], true);
                                                    if (!empty($documentos) && is_array($documentos)):
                                                        foreach ($documentos as $documento):
                                                    ?>
                                                            <a href="<?= htmlspecialchars($documento); ?>" target="_blank">Baixar</a><br>
                                                        <?php
                                                        endforeach;
                                                    else:
                                                        ?>
                                                        <p>Nenhum documento disponível.</p>
                                                    <?php
                                                    endif;
                                                    ?>
                                                </td> -->
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <a href="edit_item.php?id=<?= $row['cod_item'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                    <!-- <form action="cadastrar.php" method="POST" class="d-inline">
                                                        <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="delete_item" value="<?= $row['cod_item'] ?>" class="btn btn-danger btn-sm"><span class="bi-trash3-fill"></span>&nbsp;Excluir</button>
                                                    </form> -->
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" style="text-align: center;">Nenhum Item encontrado</td></tr>';
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