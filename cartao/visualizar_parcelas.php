<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id_compra'])) {
    $id_compra = mysqli_real_escape_string($conn, $_GET['id_compra']);

    $sql_compra = "SELECT nome_cartao, quantidade_parcelas FROM compras WHERE id_compra = $id_compra";
    $resultado_compra = $conn->query($sql_compra);

    if ($resultado_compra->num_rows > 0) {
        $compra = $resultado_compra->fetch_assoc();
        $nome_cartao = $compra['nome_cartao'];
        $quantidade_parcelas = $compra['quantidade_parcelas']; // Obtenha a quantidade total de parcelas
    } else {
        $_SESSION['mensagem'] = "ID de compra inválido.";
        $_SESSION['tipo_mensagem'] = 'danger';
        header("Location: lista_compras.php");
        exit();
    }

    $sql_parcelas = "SELECT referencia_parcela, data_vencimento, valor_parcela_responsavel1, valor_parcela_responsavel2 FROM parcelamentos WHERE id_compra = $id_compra ORDER BY data_vencimento ASC";
    $resultado_parcelas = $conn->query($sql_parcelas);
    $parcelas = [];
    if ($resultado_parcelas->num_rows > 0) {
        while ($parcela = $resultado_parcelas->fetch_assoc()) {
            $parcela['valor_parcela_responsavel1_formatado'] = number_format($parcela['valor_parcela_responsavel1'], 2, ',', '.');
            $parcela['valor_parcela_responsavel2_formatado'] = number_format($parcela['valor_parcela_responsavel2'], 2, ',', '.');
            $parcelas[] = $parcela;
        }
    }
} else {
    $_SESSION['mensagem'] = "Nenhuma compra selecionada para visualizar as parcelas.";
    $_SESSION['tipo_mensagem'] = 'danger';
    header("Location: lista_compras.php");
    exit();
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <title>Parcelas da Compra</title>
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
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

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>Parcelas da Compra - Cartão: <?php echo htmlspecialchars($nome_cartao); ?>
                            <a href="lista_compras.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($parcelas)) : ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>PARCELA</th>
                                            <th>DATA DE VENCIMENTO</th>
                                            <th>MAIARA CARLA</th>
                                            <th>CARLA MARAISA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($parcelas as $parcela) : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($parcela['referencia_parcela']) . " de " . $quantidade_parcelas; ?></td>
                                                <td><?= date('d/m/Y', strtotime($parcela['data_vencimento'])); ?></td>
                                                <td class="text-center"><?= $parcela['valor_parcela_responsavel1_formatado']; ?></td>
                                                <td class="text-center"><?= $parcela['valor_parcela_responsavel2_formatado']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : ?>
                                <p class="text-center">Nenhuma parcela encontrada para esta compra.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
