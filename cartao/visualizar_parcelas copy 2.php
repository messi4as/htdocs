<?php
session_start();
require 'db_connect.php';

// Nome do arquivo atual para o redirecionamento (substitua se necessário)
$pagina_atual = 'nome_do_seu_arquivo_de_parcelas.php'; // EX: lista_parcelas.php

if (isset($_GET['id_compra'])) {
    $id_compra = mysqli_real_escape_string($conn, $_GET['id_compra']);

    // 1. Busca dados da Compra
    $sql_compra = "SELECT nome_cartao, quantidade_parcelas FROM compras WHERE id_compra = $id_compra";
    $resultado_compra = $conn->query($sql_compra);

    if ($resultado_compra->num_rows > 0) {
        $compra = $resultado_compra->fetch_assoc();
        $nome_cartao = $compra['nome_cartao'];
        $quantidade_parcelas = $compra['quantidade_parcelas'];
    } else {
        $_SESSION['mensagem'] = "ID de compra inválido.";
        $_SESSION['tipo_mensagem'] = 'danger';
        header("Location: lista_compras.php");
        exit();
    }

    // 2. Busca dados das Parcelas (incluindo id_parcelamento)
    $sql_parcelas = "SELECT id_parcelamento, referencia_parcela, data_vencimento, valor_parcela_responsavel1, valor_parcela_responsavel2 FROM parcelamentos WHERE id_compra = $id_compra ORDER BY referencia_parcela ASC";
    // 💡 CORREÇÃO: Execução da query de parcelas estava faltando ou incorreta
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
                
                <?php
                if (isset($_SESSION['mensagem'])): 
                ?>
                    <div class="alert alert-<?php echo $_SESSION['tipo_mensagem']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['mensagem']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                    unset($_SESSION['mensagem']);
                    unset($_SESSION['tipo_mensagem']);
                endif;
                ?>
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>Parcelas da Compra - Cartão: <?php echo htmlspecialchars($nome_cartao); ?>
                                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
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
                                                <td>
                                                    <?= date('d/m/Y', strtotime($parcela['data_vencimento'])); ?>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editarDataModal"
                                                            data-id-parcela="<?= htmlspecialchars($parcela['id_parcelamento']); ?>"
                                                            data-data-atual="<?= htmlspecialchars($parcela['data_vencimento']); ?>"
                                                            data-referencia-parcela="<?= htmlspecialchars($parcela['referencia_parcela']); ?>">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </td>
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
    
    <div class="modal fade" id="editarDataModal" tabindex="-1" aria-labelledby="editarDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDataModalLabel">
                        Editar Vencimento da Parcela <span id="ref_parcela_modal"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="atualizar_data.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_parcelamento" id="id_parcelamento_modal">
                        <input type="hidden" name="id_compra" value="<?= $id_compra; ?>">
                        
                        <div class="mb-3">
                            <label for="nova_data" class="form-label">Nova Data de Vencimento</label>
                            <input type="date" class="form-control" id="nova_data" name="nova_data" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alteração</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        var editarDataModal = document.getElementById('editarDataModal');
        editarDataModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            
            // Extrai os dados dos atributos data-* do botão
            var idParcela = button.getAttribute('data-id-parcela');
            var dataAtual = button.getAttribute('data-data-atual');
            var refParcela = button.getAttribute('data-referencia-parcela');

            // Referencia os elementos de input dentro do modal
            var modalIdParcelamento = editarDataModal.querySelector('#id_parcelamento_modal');
            var modalNovaDataInput = editarDataModal.querySelector('#nova_data');
            var modalTituloRefParcela = editarDataModal.querySelector('#ref_parcela_modal');

            // Preenche os valores no formulário
            modalIdParcelamento.value = idParcela;
            modalNovaDataInput.value = dataAtual; // Define a data atual como valor inicial (YYYY-MM-DD)
            modalTituloRefParcela.textContent = refParcela;
        });
    </script>
</body>

</html>