<?php
session_start();
require 'db_connect.php';

// Consulta para buscar todos os Logs cadastrados
$sql_auditoria = "SELECT * FROM log_auditoria ORDER BY timestamp DESC"; // Ordenar por data para mostrar os mais recentes primeiro
$result_auditoria = mysqli_query($conn, $sql_auditoria);
$auditoria = mysqli_fetch_all($result_auditoria, MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>Histórico de Alterações</title>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>HISTÓRICO DE ALTERAÇÕES
                            <a href="login.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> <table class="table table-striped table-bordered table-hover" id="auditoriaTable" style="margin-top: 20px;">
                                <thead class="thead-dark"> <tr>
                                        <th style="text-align: center; vertical-align: middle;">#</th>
                                        <th style="text-align: center; vertical-align: middle;">Data/Hora</th>
                                        <th style="text-align: center; vertical-align: middle;">Operação</th>
                                        <th style="text-align: center; vertical-align: middle;">ID Registro</th>
                                        <th style="text-align: center; vertical-align: middle;">Tabela</th>
                                        <th style="text-align: center; vertical-align: middle;">Dados Anteriores</th>
                                        <th style="text-align: center; vertical-align: middle;">Dados Alterados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($auditoria)): ?>
                                        <?php foreach ($auditoria as $auditoria_item): ?>
                                            <tr data-id="<?= $auditoria_item['id']; ?>">
                                                <td style="text-align: center; vertical-align: middle;"><?= $auditoria_item['id']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y H:i:s', strtotime($auditoria_item['timestamp'])); ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $auditoria_item['operacao']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $auditoria_item['id_tabela']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $auditoria_item['nome_tabela']; ?></td>
                                                <td>
                                                    <?php
                                                    $dadosAntigos = json_decode($auditoria_item['dados_antigos'], true);
                                                    if ($dadosAntigos) {
                                                        echo "<ul style='list-style-type:none; padding: 0; margin: 0;'>";
                                                        foreach ($dadosAntigos as $chave => $valor) {
                                                            echo "<li><strong>" . htmlspecialchars($chave) . ":</strong> " . htmlspecialchars(strip_tags($valor)) . "</li>";
                                                        }
                                                        echo "</ul>";
                                                    } else {
                                                        echo "-";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $dadosNovos = json_decode($auditoria_item['dados_novos'], true);
                                                    if ($dadosNovos) {
                                                        echo "<ul style='list-style-type:none; padding: 0; margin: 0;'>";
                                                        foreach ($dadosNovos as $chave => $valor) {
                                                            echo "<li><strong>" . htmlspecialchars($chave) . ":</strong> " . htmlspecialchars(strip_tags($valor)) . "</li>";
                                                        }
                                                        echo "</ul>";
                                                    } else {
                                                        echo "-";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhum log de auditoria encontrado.</td>
                                        </tr>
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