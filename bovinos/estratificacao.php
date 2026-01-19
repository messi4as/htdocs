<?php
session_start();
require 'db_connect.php';

// Recuperar os status do banco de dados (Ainda útil se quiser permitir buscar por 'MORTE')
$status_query = "SELECT DISTINCT status FROM bovinos_com_idade";
$status_result = mysqli_query($conn, $status_query);

// Recuperar as estratificações APENAS de animais ATIVOS para o filtro e o modal
$estratificacao_query = "SELECT DISTINCT estratificacao FROM bovinos_com_idade WHERE status = 'ATIVO' AND estratificacao IS NOT NULL AND estratificacao != '' ORDER BY estratificacao ASC";
$estratificacao_result = mysqli_query($conn, $estratificacao_query);
$estratificacao_modal_result = mysqli_query($conn, $estratificacao_query); // Cópia para o modal

// Query para buscar AGRUPAMENTOS de animais ATIVOS (necessário para o modal)
$agrupamento_query = "SELECT DISTINCT agrupamento FROM bovinos_com_idade WHERE status = 'ATIVO' AND agrupamento IS NOT NULL AND agrupamento != '' ORDER BY agrupamento ASC";
$agrupamento_modal_result = mysqli_query($conn, $agrupamento_query);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>GESTÃO DE BOVINOS - ATIVOS</title>

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

</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>BOVINOS ATIVOS - FAZENDA ROSADA
                                <div class="float-end">

                                   
                                    
                                    <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#modalAtualizarAgrupamento">
                                        <span class="bi bi-person-lines-fill"></span>&nbsp;Atualizar Agrupamento
                                    </button>

                                    <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#modalAtualizarEstratificacao">
                                        <span class="bi bi-arrow-repeat"></span>&nbsp;Atualizar Estratificação
                                    </button>
                                    
                                  
                                </div>
                            </h4>
                        </div>
                        <div class="card-body">

                            <form method="GET" action="" class="mb-1">
                                <div class="input-group mb-3">
                                    <input type="text" name="brinco" class="form-control" placeholder="Buscar por Brinco (separados por vírgula)" value="<?= htmlspecialchars($_GET['brinco'] ?? '') ?>"> 
                                    
                                    <select name="estratificacao" class="form-control">
                                        <option value="">Buscar por Estratificação</option>
                                        <?php
                                        mysqli_data_seek($estratificacao_result, 0); // Reinicia o ponteiro
                                        while ($row = mysqli_fetch_assoc($estratificacao_result)) {
                                            $selected = (isset($_GET['estratificacao']) && $_GET['estratificacao'] == $row['estratificacao']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['estratificacao']) . '" ' . $selected . '>' . htmlspecialchars($row['estratificacao']) . '</option>';
                                        }
                                        ?>
                                    </select> 
                                    
                                    <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                                </div>
                            </form>

                            <?php
                            $brinco = mysqli_real_escape_string($conn, $_GET['brinco'] ?? '');
                            $estratificacao = mysqli_real_escape_string($conn, $_GET['estratificacao'] ?? '');

                            // QUERY PADRÃO 'STATUS = ATIVO'
                            $sql = "SELECT * FROM bovinos_com_idade WHERE status = 'ATIVO'";
                            
                            if ($brinco != '') {
                                $brincos = explode(',', $brinco);
                                $brincos = array_map('trim', $brincos);
                                $brinco_conditions = array();
                                foreach ($brincos as $b) {
                                    $brinco_conditions[] = "brinco LIKE '%" . mysqli_real_escape_string($conn, $b) . "%'";
                                }
                                $sql .= " AND (" . implode(' OR ', $brinco_conditions) . ")";
                            }
                            
                            // FILTRO POR ESTRATIFICAÇÃO
                            if ($estratificacao != '') {
                                $sql .= " AND estratificacao LIKE '%" . mysqli_real_escape_string($conn, $estratificacao) . "%'";
                            }
                            
                            $sql .= " ORDER BY idade"; // Ordenar pelo brinco de forma decrescente
                            $bovino = mysqli_query($conn, $sql);
                            $quantidade = mysqli_num_rows($bovino);
                            ?>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Bovinos **Ativos** Encontrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">BRINCO</th>
                                        <th style="text-align: center;">IDADE (MESES)</th>
                                        <th style="text-align: center;">LOCAL</th>
                                        <th style="text-align: center;">SEXO</th>
                                        <th style="text-align: center;">GRUPO</th>
                                        <th style="text-align: center;">ESTRATIFICAÇÃO</th>
                                        <th style="text-align: center;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($quantidade > 0) {
                                        while ($bovinos = mysqli_fetch_assoc($bovino)) {
                                            $idade = $bovinos['idade'];
                                            $idade_texto = $idade . ' ' . ($idade == 1 ? 'mês' : 'meses');
                                    ?>

                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($bovinos['brinco']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($idade_texto) ?></td>
                                                <td style="text-align: left; vertical-align: middle;"><?= htmlspecialchars($bovinos['local']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($bovinos['sexo']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($bovinos['agrupamento']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($bovinos['estratificacao']) ?></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <a href="view_animal.php?id=<?= htmlspecialchars($bovinos['cod_animal']) ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="7"><h5>Nenhum Animal Ativo Encontrado</h5></td></tr>';
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

    <div class="modal fade" id="modalAtualizarEstratificacao" tabindex="-1" aria-labelledby="modalAtualizarEstratificacaoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="update_estratificacao.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAtualizarEstratificacaoLabel">Atualizar Estratificação de Bovinos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="brincosInput" class="form-label">Brincos (separados por vírgula):</label>
                            <textarea class="form-control" id="brincosInput" name="brincos" rows="3" placeholder="Ex: 6001,6002, 6003" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="novaEstratificacao" class="form-label">Nova Estratificação:</label>
                            <select class="form-select" id="novaEstratificacao" name="nova_estratificacao" required>
                                <option value="" selected disabled>Selecione a Estratificação</option>
                                <?php
                                mysqli_data_seek($estratificacao_modal_result, 0);
                                while ($row = mysqli_fetch_assoc($estratificacao_modal_result)) {
                                    echo '<option value="' . htmlspecialchars($row['estratificacao']) . '">' . htmlspecialchars($row['estratificacao']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAtualizarAgrupamento" tabindex="-1" aria-labelledby="modalAtualizarAgrupamentoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="update_agrupamento.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAtualizarAgrupamentoLabel">Atualizar Agrupamento de Bovinos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="brincosAgrupamentoInput" class="form-label">Brincos (separados por vírgula):</label>
                            <textarea class="form-control" id="brincosAgrupamentoInput" name="brincos" rows="3" placeholder="Ex: 6001,6002, 6003" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="novoAgrupamento" class="form-label">Novo Agrupamento:</label>
                            <select class="form-select" id="novoAgrupamento" name="novo_agrupamento" required>
                                <option value="" selected disabled>Selecione o Agrupamento</option>
                                <?php
                                // Reinicia o ponteiro da query de agrupamento
                                mysqli_data_seek($agrupamento_modal_result, 0); 
                                while ($row = mysqli_fetch_assoc($agrupamento_modal_result)) {
                                    echo '<option value="' . htmlspecialchars($row['agrupamento']) . '">' . htmlspecialchars($row['agrupamento']) . '</option>';
                                }
                                ?>
                                <option value="NOVO_GRUPO">Criar Novo Grupo (Selecione, e digite no campo abaixo)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                             <label for="nomeNovoAgrupamento" class="form-label">Nome do Novo Agrupamento (Opcional):</label>
                            <input type="text" class="form-control" id="nomeNovoAgrupamento" name="nome_novo_agrupamento" placeholder="Digite o nome do novo grupo se necessário">
                             <small class="form-text text-muted">Use este campo apenas se selecionar "Criar Novo Grupo" acima.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Atualizar Agrupamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>