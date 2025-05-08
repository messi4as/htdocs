<?php
session_start();
require 'db_connect.php';

// Função para obter opções únicas de uma coluna
function getOptions($conn, $column)
{
    $options = [];
    $sql = "SELECT DISTINCT $column FROM bovinos WHERE status = 'ATIVO'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row[$column];
    }
    return $options;
}

$localOptions = getOptions($conn, 'local');
$loteOptions = getOptions($conn, 'lote');
$agrupamentoOptions = getOptions($conn, 'agrupamento');
$situacaoAtualOptions = getOptions($conn, 'situacao_atual');
// Função para obter dados dos bovinos com filtros
function getBovinosData($conn, $local, $lote, $agrupamento, $situacao_atual)
{
    $sql = "SELECT local, lote, agrupamento, situacao_atual, COUNT(*) as quantidade 
            FROM bovinos 
            WHERE status = 'ATIVO'";
    $conditions = [];
    if ($local != '') {
        $conditions[] = "local LIKE '%$local%'";
    }
    if ($lote != '') {
        $conditions[] = "lote LIKE '%$lote%'";
    }
    if ($agrupamento != '') {
        $conditions[] = "agrupamento LIKE '%$agrupamento%'";
    }
    if ($situacao_atual != '') {
        $conditions[] = "situacao_atual LIKE '%$situacao_atual%'";
    }
    if (count($conditions) > 0) {
        $sql .= " AND " . implode(' AND ', $conditions);
    }
    $sql .= " GROUP BY agrupamento";
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

$local = isset($_GET['local']) ? $_GET['local'] : '';
$lote = isset($_GET['lote']) ? $_GET['lote'] : '';
$agrupamento = isset($_GET['agrupamento']) ? $_GET['agrupamento'] : '';
$situacao_atual = isset($_GET['situacao_atual']) ? $_GET['situacao_atual'] : '';

$bovinosData = getBovinosData($conn, $local, $lote, $agrupamento, $situacao_atual);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>VISUALIZAÇÃO DE BOVINOS</title>
   
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div id="mensagem"><?php include('mensagem.php'); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="table-container">
                            <div class="card-header">
                                <h4>VISUALIZAÇÃO DE BOVINOS PARA CONFERÊNCIA<div class="float-end">
                                </h4>

                            </div>

                            <div class="card-body">

                                <form method="GET" action="" class="mb-1">
                                    <div class="input-group mb-3">
                                        <select name="local" class="form-control">
                                            <option value="">Local</option>
                                            <?php foreach ($localOptions as $option) : ?>
                                                <option value="<?= $option ?>" <?= $local == $option ? 'selected' : '' ?>><?= $option ?></option>
                                            <?php endforeach; ?>
                                        </select> &nbsp; &nbsp;
                                        <select name="lote" class="form-control">
                                            <option value="">Lote</option>
                                            <?php foreach ($loteOptions as $option) : ?>
                                                <option value="<?= $option ?>" <?= $lote == $option ? 'selected' : '' ?>><?= $option ?></option>
                                            <?php endforeach; ?>
                                        </select> &nbsp; &nbsp;

                                        <select name="agrupamento" class="form-control">
                                            <option value="">Grupo</option>
                                            <?php foreach ($agrupamentoOptions as $option) : ?>
                                                <option value="<?= $option ?>" <?= $agrupamento == $option ? 'selected' : '' ?>><?= $option ?></option>
                                            <?php endforeach; ?>
                                        </select> &nbsp; &nbsp;
                                        <select name="situacao_atual" class="form-control">
                                            <option value="">Situação Atual</option>
                                            <?php foreach ($situacaoAtualOptions as $option) : ?>
                                                <option value="<?= $option ?>" <?= $situacao_atual == $option ? 'selected' : '' ?>><?= $option ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button class="btn btn-primary me-2 float-end" type="submit">Filtrar</button>
                                    <button type="button" class="btn btn-success me-2 float-end" data-bs-toggle="modal" data-bs-target="#ocorrenciaModal">Adicionar Ocorrência</button>
                                    <!-- Novo botão para atualizar local e lote -->
                                    <button type="button" class="btn btn-secondary me-2 float-end" data-bs-toggle="modal" data-bs-target="#atualizarModal">Atualizar Local e Lote</button>
                                    <br>
                                    <br>
                                </form>


                                <?php
                                $local = isset($_GET['local']) ? $_GET['local'] : '';
                                $lote = isset($_GET['lote']) ? $_GET['lote'] : '';
                                $agrupamento = isset($_GET['agrupamento']) ? $_GET['agrupamento'] : '';
                                $situacao_atual = isset($_GET['situacao_atual']) ? $_GET['situacao_atual'] : '';

                                $sql = "SELECT brinco, 
                       TIMESTAMPDIFF(MONTH, data_nascimento, CURDATE()) AS idade, 
                       agrupamento, 
                       situacao_atual, 
                       local, 
                       lote 
                FROM bovinos
                WHERE status = 'ATIVO'";
                                $conditions = [];
                                if ($local != '') {
                                    $conditions[] = "local LIKE '%$local%'";
                                }
                                if ($lote != '') {
                                    $conditions[] = "lote LIKE '%$lote%'";
                                }
                                if ($agrupamento != '') {
                                    $conditions[] = "agrupamento LIKE '%$agrupamento%'";
                                }
                                if ($situacao_atual != '') {
                                    $conditions[] = "situacao_atual LIKE '%$situacao_atual%'";
                                }
                                if (count($conditions) > 0) {
                                    $sql .= " AND " . implode(' AND ', $conditions);
                                }
                                $result = mysqli_query($conn, $sql);
                                $quantidade = mysqli_num_rows($result);
                                ?>

                                <!-- Exibir Quantidade -->
                                <div class="alert alert-info" role="alert">
                                    Quantidade de Bovinos Ativos: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                                </div>


                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style='text-align: center; vertical-align: middle;'>BRINCO</th>
                                            <th style='text-align: center; vertical-align: middle;'>IDADE (MESES)</th>
                                            <th style='text-align: center; vertical-align: middle;'>GRUPO</th>
                                            <th style='text-align: center; vertical-align: middle;'>SITUAÇÃO ATUAL</th>
                                            <th style='text-align: center; vertical-align: middle;'>LOCAL</th>
                                            <th style='text-align: center; vertical-align: middle;'>LOTE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($quantidade > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td style='text-align: center; vertical-align: middle;'>" . $row['brinco'] . "</td>";
                                                echo "<td style='text-align: center; vertical-align: middle;'>" . $row['idade'] . " meses</td>";
                                                echo "<td style='text-align: center; vertical-align: middle;'>" . $row['agrupamento'] . "</td>";
                                                echo "<td style='text-align: center; vertical-align: middle;'>" . $row['situacao_atual'] . "</td>";
                                                echo "<td style='text-align: center; vertical-align: middle;'>" . $row['local'] . "</td>";
                                                echo "<td style='text-align: center; vertical-align: middle;'>" . $row['lote'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>Nenhum bovino encontrado</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <!-- Modal para Adicionar Ocorrência -->
                                <div class="modal fade" id="ocorrenciaModal" tabindex="-1" aria-labelledby="ocorrenciaModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="ocorrenciaModalLabel">Adicionar Ocorrência</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="ocorrenciaForm" method="POST" action="adicionar_ocorrencia.php">
                                                    <div class="mb-3">
                                                        <label for="brincos" class="form-label">Brincos (separados por vírgulas)</label>
                                                        <input type="text" class="form-control" id="brincos" name="brincos" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="data" class="form-label">Data</label>
                                                        <input type="date" class="form-control" id="data" name="data" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="local" class="form-label">Local</label>
                                                        <input type="text" class="form-control" id="local" name="local" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="tipo" class="form-label">Tipo</label>
                                                        <input type="text" class="form-control" id="tipo" name="tipo" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="peso" class="form-label">Peso</label>
                                                        <input type="text" class="form-control" id="peso" name="peso">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="descricao" class="form-label">Descrição</label>
                                                        <textarea class="form-control" id="descricao" name="descricao" required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Adicionar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Novo Modal para Atualizar Local e Lote -->
                            <div class="modal fade" id="atualizarModal" tabindex="-1" aria-labelledby="atualizarModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="atualizarModalLabel">Atualizar Local e Lote</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="atualizarForm" method="POST" action="atualizar_local_lote.php">
                                                <div class="mb-3">
                                                    <label for="brincos" class="form-label">Brincos (separados por vírgulas)</label>
                                                    <input type="text" class="form-control" id="brincos" name="brincos" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="brincos" class="form-label">Novo Local</label>
                                                    <input type="text" class="form-control" id="novo_local" name="novo_local" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="novo_lote" class="form-label">Novo Lote</label>
                                                    <input type="text" class="form-control" id="novo_lote" name="novo_lote" required>
                                                </div>
                                                <button type="submit" class="btn btn-secondary">Atualizar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
    $(document).ready(function() {
        $('#atualizarForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'atualizar_local_lote.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    $('#mensagem').html('<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                        response.mensagem +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    $('#atualizarModal').modal('hide');
                    setTimeout(function() {
                        location.reload(); // Recarregar a página após um pequeno atraso
                    }, 2000); // Atraso de 2 segundos
                },
                error: function(xhr, status, error) {
                    $('#mensagem').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        'Erro ao atualizar: ' + error +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                }
            });
        });

        $('#ocorrenciaForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'adicionar_ocorrencia.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    $('#mensagem').html('<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                        response.mensagem +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    $('#ocorrenciaModal').modal('hide');
                    setTimeout(function() {
                        location.reload(); // Recarregar a página após um pequeno atraso
                    }, 1500); // Atraso de 1,5 segundos
                },
                error: function(xhr, status, error) {
                    $('#mensagem').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        'Erro ao adicionar ocorrência: ' + error +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                }
            });
        });
    });
</script>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Bovinos: <?php echo $quantidade; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>