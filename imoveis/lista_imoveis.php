<?php
session_start();
require 'db_connect.php';

// Obter todos os imóveis do banco de dados
$nome = '';
if (isset($_GET['nome_imovel'])) {
    $nome = mysqli_real_escape_string($conn, $_GET['nome_imovel']);
}

$sql = "SELECT * FROM imoveis";
if ($nome != '') {
    $sql .= " WHERE nome_imovel LIKE '%$nome%'";
}
$sql .= " ORDER BY nome_imovel";
$result = $conn->query($sql);
$imovel = mysqli_query($conn, $sql);

$quantidade = mysqli_num_rows($imovel);
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

    <title>Lista de Imóveis</title>

   <style>
    /* Estilos gerais */
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

    /* Novo estilo para a célula do proprietário */
    .proprietario-cell {
        white-space: normal;
        line-height: 1.2;
    }

    .proprietario-cell p,
    .proprietario-cell div {
        margin-bottom: 0;
        padding: 0;
    }

    /* Estilos para Impressão */
    @media print {
        /* Esconde elementos desnecessários para a impressão */
        .no-print,
        .btn,
        .card-header a,
        form,
        #mensagem-php,
        .col-acoes {
            display: none !important;
        }

        /* Ajusta o layout geral */
        body {
            font-size: 10pt;
            padding: 0;
            margin: 0;
            background-color: #fff;
        }
        
        /* Limpa estilos do Bootstrap para não interferirem na centralização */
        .container,
        .card,
        .card-body,
        .row,
        .col-md-12 {
            padding: 0;
            margin: 0;
            box-shadow: none;
            border: none;
            width: 100% !important;
            max-width: none !important;
        }
        
        /* Centraliza o cabeçalho e a tabela */
        h4 {
            text-align: center;
            margin: 6px auto; /* Adiciona margem superior/inferior e centraliza horizontalmente */
        }
        
        .table-container {
            width: 95%; /* Define uma largura para a tabela */
            margin: 0 auto; /* Centraliza o contêiner na página */
            overflow-x: visible !important;
        }
        
        table {
            width: 100%; /* Garante que a tabela ocupe 100% do seu contêiner pai */
            font-size: 9pt;
            page-break-inside: auto;
        }
        
        th,
        td {
            padding: 4px;
            word-wrap: break-word;
        }
    }
</style>

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
                            <h4>LISTA DE IMÓVEIS
                                <a href="cadastro_imoveis.php" class="btn btn-primary float-end"><span class="bi-plus-circle-fill"></span>&nbsp;Adicionar Imóvel</a>
                                <button onclick="window.print()" class="btn btn-info float-end me-2"><span class="bi-printer-fill"></span>&nbsp;Imprimir</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="lista_imoveis.php">
                                <div class="input-group mb-3">
                                    <input type="text" name="nome_imovel" class="form-control" placeholder="Buscar por Nome">
                                    <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                                </div>
                            </form>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Imóveis Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered table-striped table-hover table-sm table-responsive">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; width:100px;" class="col-acoes">LOCALIZAÇÃO</th>
                                        <th style="text-align: center;">NOME</th>
                                        <th style="text-align: center;">BAIRRO</th>
                                        <th style="text-align: center; width:400px;">PROPRIETÁRIO</th>
                                        <th style="text-align: center;" class="col-acoes">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;" class="col-acoes">
                                                    <a href="<?= $row['localizacao_imovel']; ?>" target="_blank"><span class="bi-eye-fill"></span>&nbsp;Localizar</a>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['nome_imovel']; ?></td>
                                                <td style="text-align: left; vertical-align: middle;"><?= $row['bairro_imovel']; ?></td>
                                                <td class="proprietario-cell" style="text-align: left; vertical-align: middle;"><?= stripslashes($row['proprietario_imovel']); ?></td>
                                                <td style="text-align: center; vertical-align: middle;" class="col-acoes">
                                                    <a href="edit_imoveis.php?id=<?= $row['cod_imovel'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" style="text-align: center;">Nenhum imóvel encontrado</td></tr>';
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