<?php
session_start();
require 'db_connect.php';

// 1. Busca todas as funções ordenadas pelo ID (para manter a hierarquia)
$sql_funcoes = "SELECT id, nome_funcao, descricao FROM Funcoes ORDER BY id ASC";
$result_funcoes = $conn->query($sql_funcoes);
$funcoes = $result_funcoes->fetch_all(MYSQLI_ASSOC);

// 2. Busca todas as atividades por departamento para acesso rápido
$atividades_por_departamento = [];
$sql_atividades = "SELECT departamento_id, nome_atividade FROM Atividades ORDER BY nome_atividade ASC";
$result_atividades = $conn->query($sql_atividades);
while ($row = $result_atividades->fetch_assoc()) {
    $atividades_por_departamento[$row['departamento_id']][] = $row['nome_atividade'];
}
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
    <title>ORGANOGRAMA DINÂMICO</title>

    <style>
        .center-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .organograma-container {
            max-width: 900px;
            width: 100%;
        }

        .card-organograma {
            margin-bottom: 20px;
            text-align: left;
        }

        .card-header-funcao {
            background-color: #3d3f41ff;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .card-organograma .list-group-item {
            border: none;
            padding: 0.5rem 1rem;
        }

        .card-organograma strong {
            color: #0056b3;
        }

        .funcionario-item {
            display: block;
            margin-bottom: 10px;
        }

        .atividades-list {
            margin-top: 5px;
            margin-bottom: 0;
            padding-left: 20px;
        }
    </style>
    <style type="text/css" media="print">
        /* Oculta a barra de navegação e os botões de impressão */
        .navbar, .btn {
            display: auto !important;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100% !important;
            padding: 0;
            margin: 0;
        }

        .card-organograma {
            border: 1px solid #0a0a0aff !important;
            margin-bottom: 1em;
        }
        
        .card-header-funcao {
            background-color: #3d3f41ff !important;
            color: white !important;
            padding: 0.5em;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #3d3f41ff !important;
        }

        .card-body, .card-header {
            background-color: transparent !important;
            border: none !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        h4 {
            color: #f5ededff !important;
        }

        hr {
            border-top: 1px solid #ddd;
        }

        ul, li {
            margin: 0;
            padding-left: 15px;
        }

        .atividades-list {
            padding-left: 10px;
        }
    </style>
</head>

<body>
    <?php include('../navbar.php'); ?>
    <div class="container mt-4">
        <div class="row d-flex justify-content-center">
            <div class="col-md-11">
                <div class="card card-organograma">
                    <div class="card-header-funcao">
                        <h4>ORGANOGRAMA ESCRITÓRIO M2 SHOWS</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($funcoes)): ?>
                            <div class="alert alert-warning text-center">Nenhuma função encontrada. Por favor, cadastre no painel de gerenciamento.</div>
                        <?php else: ?>
                            <?php foreach ($funcoes as $funcao): ?>
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5><?= htmlspecialchars($funcao['nome_funcao']) ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <?php 
                                        $sql_funcionarios = "
                                            SELECT f.id, f.nome, GROUP_CONCAT(DISTINCT d.id ORDER BY d.nome_departamento ASC) AS departamentos_ids, GROUP_CONCAT(DISTINCT d.nome_departamento ORDER BY d.nome_departamento ASC) AS departamentos_nomes
                                            FROM Funcionarios f
                                            LEFT JOIN Funcionarios_Departamentos fd ON fd.funcionario_id = f.id
                                            LEFT JOIN Departamentos d ON d.id = fd.departamento_id
                                            WHERE f.funcao_id = ?
                                            GROUP BY f.id
                                            ORDER BY f.nome ASC
                                        ";
                                        $stmt = $conn->prepare($sql_funcionarios);
                                        $stmt->bind_param("i", $funcao['id']);
                                        $stmt->execute();
                                        $result_funcionarios = $stmt->get_result();
                                        
                                        if ($result_funcionarios->num_rows > 0) {
                                            while ($funcionario = $result_funcionarios->fetch_assoc()): 
                                                $departamentos_ids = explode(',', $funcionario['departamentos_ids']);
                                                $departamentos_nomes = explode(',', $funcionario['departamentos_nomes']);
                                            ?>
                                                <div class="funcionario-item">
                                                    <strong><?= htmlspecialchars($funcionario['nome']) ?></strong>
                                                    <?php if (!empty($departamentos_nomes) && $departamentos_nomes[0] != ''): ?>
                                                        <br>
                                                        <?php for ($i = 0; $i < count($departamentos_ids); $i++): 
                                                            $depto_id = $departamentos_ids[$i];
                                                            $depto_nome = $departamentos_nomes[$i];
                                                            $atividades_deste_depto = isset($atividades_por_departamento[$depto_id]) ? $atividades_por_departamento[$depto_id] : [];
                                                        ?>
                                                            <div class="ms-3 mt-2">
                                                                <small class="text-muted">Departamento: <?= htmlspecialchars($depto_nome) ?></small>
                                                                <ul class="atividades-list">
                                                                    <?php if (!empty($atividades_deste_depto)): ?>
                                                                        <?php foreach ($atividades_deste_depto as $atividade): ?>
                                                                            <li><?= htmlspecialchars($atividade) ?></li>
                                                                        <?php endforeach; ?>
                                                                    <?php else: ?>
                                                                        <li>Nenhuma atividade definida para este departamento.</li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </div>
                                                        <?php endfor; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <hr>
                                            <?php endwhile;
                                        } else {
                                            echo "<p class='text-muted'>Nenhum funcionário com este cargo.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>