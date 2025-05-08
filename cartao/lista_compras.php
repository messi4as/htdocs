<?php
session_start();
require 'db_connect.php';

// Obter todos as compras do banco de dados
$nome_cartao_filtro = '';
if (isset($_GET['nome_cartao'])) {
    $nome_cartao_filtro = mysqli_real_escape_string($conn, $_GET['nome_cartao']);
}

$sql = "SELECT id_compra, nome_cartao, data_compra, valor, quantidade_parcelas, anexo FROM compras";
if (!empty($nome_cartao_filtro)) {
    $sql .= " WHERE nome_cartao LIKE '%$nome_cartao_filtro%'";
}
$sql .= " ORDER BY data_compra DESC"; // Ordena por data da compra, mais recente primeiro
$resultado_compras = $conn->query($sql);

$quantidade = $resultado_compras->num_rows;

// Função para formatar o valor para exibição
function formatar_valor($valor)
{
    // Substitui a vírgula por ponto para garantir que o valor esteja no formato correto
    $valor = str_replace(',', '.', $valor);
    // Converte para float para garantir que é um número
    $valor = floatval($valor);
    return number_format($valor, 2, ',', '.');
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
        text-align: center;
    }

    th {
        background-color: #f2f2f2;
    }

    /* Estilos para status (se você for implementar isso) */
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
        height: 15px;
        background-color: #f0f0f0;
        border-radius: 5px;
        overflow: hidden;
        margin: 0 auto;
    }

    .progress-bar {
        height: 100%;
        background-color: rgba(11, 62, 73, 0.5);
        width: 0%;
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
    <title>Lista de Compras</title>
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
                            <h4>LISTA DE COMPRAS REGISTRADAS EM CARTÕES DE CRÉDITO

                                <a href="fatura_compras.php" class="btn btn-warning me-2 float-end"><span class="bi bi-bar-chart"></span>&nbsp;Fatura</a>
                                <a href="cadastro_compras.php" class="btn btn-primary me-2 float-end"><span class="bi-plus-circle-fill"></span>&nbsp;Adicionar Compra</a>

                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="lista_compras.php">
                                <div class="input-group mb-3">
                                    <input type="text" name="nome_cartao" class="form-control" placeholder="Buscar por Nome do Cartão" value="<?= $nome_cartao_filtro ?>">
                                    <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Filtrar</button>
                                </div>
                            </form>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Compras Cadastradas: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;">NOME DO CARTÃO</th>
                                        <th style="text-align: center; vertical-align: middle;">DATA DA COMPRA</th>
                                        <th style="text-align: center; vertical-align: middle;">VALOR</th>
                                        <th style="text-align: center; vertical-align: middle;">QTD DE PARCELAS</th>
                                        <th style="text-align: center; vertical-align: middle;">COMPROVANTE(S)</th>
                                        <th style="text-align: center; vertical-align: middle;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($resultado_compras->num_rows > 0) :
                                        while ($compra = $resultado_compras->fetch_assoc()) :
                                    ?>
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($compra['nome_cartao']); ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($compra['data_compra'])); ?></td>
                                                <td style="text-align: center; vertical-align: middle; width:150px"><?= formatar_valor($compra['valor']); ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($compra['quantidade_parcelas']); ?></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <?php
                                                    $comprovantes = json_decode($compra['anexo'], true);
                                                    if (!empty($comprovantes) && is_array($comprovantes)) :
                                                        foreach ($comprovantes as $comprovante) :
                                                    ?>
                                                                <a href="<?= htmlspecialchars($comprovante); ?>" target="_blank"><span class="bi bi-box-arrow-down"></span>&nbsp;Ver Comprovante</a><br>
                                                            <?php
                                                        endforeach;
                                                    else :
                                                        ?>
                                                        <p>Nenhum Comprovante Anexado.</p>
                                                    <?php
                                                    endif;
                                                    ?>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <a href="visualizar_parcelas.php?id_compra=<?= $compra['id_compra'] ?>" class="btn btn-info btn-sm"><span class="bi-list-check"></span>&nbsp;Parcelas</a>
                                                    <a href="edit_compra.php?id=<?= $compra['id_compra'] ?>" class="btn btn-secondary btn-sm"><span class="bi-pencil-fill"></span>&nbsp;Editar</a>
                                                </td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    else :
                                        ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center;">Nenhuma compra cadastrada.</td>
                                        </tr>
                                    <?php
                                    endif;
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
