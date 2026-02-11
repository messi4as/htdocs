<?php 
include('db_connect.php'); 
include('/xampp/htdocs/navbar.php'); 

$id_repositorio = 9999; // ID que criamos acima

// Busca os volumes no repositório
$sql = "SELECT * FROM volumes_arquivos WHERE caixa_id = $id_repositorio ORDER BY data_cadastro DESC";
$res = $conn->query($sql);
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Repositório Digital</title>
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h5><i class="bi bi-cloud-check"></i> Repositório Digital - Volumes Incinerados</h5>
            <a href="index.php" class="btn btn-sm btn-outline-light">Voltar à Estante</a>
        </div>
        <div class="card-body">
            <div class="alert alert-warning small">
                <i class="bi bi-exclamação-triangle"></i> Estes volumes foram incinerados fisicamente. Apenas o registro digital e anexos estão disponíveis.
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Responsável</th>
                        <th>Período</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($vol = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $vol['descricao'] ?></td>
                        <td><?= $vol['responsavel'] ?></td>
                        <td><?= $vol['data_inicio'] ? date('d/m/Y', strtotime($vol['data_inicio'])) : '-' ?></td>
                        <td>
                            <?php if($vol['tipo_fonte'] == 'link'): 
                                $linkFinanceiro = "http://desktop-server/financeiro/index.php?cod_financeiro=&data_inicio=".$vol['data_inicio']."&data_fim=".$vol['data_fim']."&responsavel=".urlencode($vol['responsavel']);
                            ?>
                                <a href="<?= $linkFinanceiro ?>" target="_blank" class="btn btn-sm btn-primary">Financeiro</a>
                            <?php else: ?>
                                <a href="<?= $vol['caminho_anexo'] ?>" target="_blank" class="btn btn-sm btn-danger">PDF</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>