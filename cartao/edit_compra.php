<?php
session_start();
require 'db_connect.php';

// Função para escapar e validar dados
function validarDados($dado)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($dado));
}

// Função auxiliar para desformatar o valor (remova pontos e substitua vírgula por ponto)
function unformat_valor($valor_formatado)
{
    return str_replace(',', '.', str_replace('.', '', $valor_formatado));
}

// Função para formatar o valor para exibição
function formatar_valor($valor)
{
    return number_format($valor, 2, ',', '.');
}

// Verificar se o ID da compra foi passado na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_compra = validarDados($_GET['id']);

    // Consultar o banco de dados para obter os dados da compra
    $sql_compra = "SELECT * FROM compras WHERE id_compra = $id_compra";
    $resultado_compra = $conn->query($sql_compra);

    if ($resultado_compra->num_rows == 1) {
        $compra = $resultado_compra->fetch_assoc();
        // Os dados da compra agora estão na variável $compra
        $compra['valor_formatado'] = formatar_valor($compra['valor']); // Adiciona o valor formatado para uso no formulário

        if (isset($compra['anexo'])) {
            $documentos = json_decode($compra['anexo'], true);
            if (!is_array($documentos)) {
                $documentos = []; // Garante que $documentos é sempre um array
            }
        } else {
            $documentos = [];
        }
    } else {
        // Se o ID não for encontrado, redirecionar para a lista de compras ou exibir uma mensagem de erro
        $_SESSION['mensagem'] = "ID da compra inválido.";
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: lista_compras.php");
        exit();
    }

    // Consultar o banco de dados para obter os parcelamentos relacionados a esta compra
    $sql_parcelamentos = "SELECT * FROM parcelamentos WHERE id_compra = $id_compra ORDER BY referencia_parcela ASC";
    $resultado_parcelamentos = $conn->query($sql_parcelamentos);
    $parcelamentos = [];
    if ($resultado_parcelamentos->num_rows > 0) {
        while ($parcela = $resultado_parcelamentos->fetch_assoc()) {
            $parcelamentos[] = $parcela;
        }
    }
} else {
    // Se o ID não foi passado ou não é numérico, redirecionar
    $_SESSION['mensagem'] = "Nenhum ID de compra fornecido.";
    $_SESSION['tipo_mensagem'] = "danger";
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
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>



    <style>
        .form-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        textarea,
        select {
            text-transform: none;
            width: 100%;
        }

        #preview-container {
            width: 200px;
            height: 200px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 10px;
        }

        #preview {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: none;
        }

        #document-preview-container {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }

        iframe {
            width: 100%;
            height: 200px;
            border: 1px solid #ddd;
        }

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

        .documento-item {
            margin-bottom: 10px;
        }

        .documento-link {
            margin-right: 10px;
        }

        .documento-botao {
            width: 80px;
            height: 40px;
        }



        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>

    <title>EDITAR CONTRATOS</title>

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
                            <h4>EDITAR COMPRA
                                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formCadastro" enctype="multipart/form-data" onsubmit="return false;">
                                <input type="hidden" name="id_compra" id="id_compra" value="<?= htmlspecialchars($compra['id_compra']) ?>">
                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label" style="width:400px;">&nbsp;NOME DO CARTÃO:</label>
                                        <select name="nome_cartao" class="form-control" required>
                                            <option value="" disabled>Selecione o Cartão</option>
                                            <option value="CARTÃO M2" <?= ($compra['nome_cartao'] == 'CARTÃO M2') ? 'selected' : '' ?>>CARTÃO M2</option>
                                            <option value="CARTÃO MAIARA CARLA" <?= ($compra['nome_cartao'] == 'CARTÃO MAIARA CARLA') ? 'selected' : '' ?>>CARTÃO MAIARA CARLA</option>
                                            <option value="CARTÃO CARLA MARAISA" <?= ($compra['nome_cartao'] == 'CARTÃO CARLA MARAISA') ? 'selected' : '' ?>>CARTÃO CARLA MARAISA</option>
                                            <option value="CARTÃO BNDES" <?= ($compra['nome_cartao'] == 'CARTÃO BNDES') ? 'selected' : '' ?>>CARTÃO BNDES</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;DATA DA COMPRA:</label>
                                        <input type="date" name="data_compra" required class="form-control" value="<?= $compra['data_compra'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;QUANTIDADE DE PARCELAS:</label>
                                        <input type="number" name="quantidade_parcelas" required class="form-control" min="1" value="<?= $compra['quantidade_parcelas'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;VALOR:</label>
                                        <input id="valor" type="text" name="valor" required class="form-control" value="<?= $compra['valor_formatado'] ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">&nbsp;PRIMEIRA DATA DE VENCIMENTO:</label>
                                    <input type="date" name="primeira_vencimento" required class="form-control">
                                </div>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;PERCENTUAL MAIARA CARLA (%):</label>
                                        <input type="number" name="percentual_responsavel1" value="50" min="0" max="100" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;PERCENTUAL CARLA MARAISA (%):</label>
                                        <input type="number" name="percentual_responsavel2" value="50" min="0" max="100" required class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">&nbsp;DESCRIÇÃO:</label>
                                    <textarea name="descricao" class="form-control" style="height:150px;"><?= $compra['descricao']; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">&nbsp;ANEXO:</label>
                                    <input id="anexo" type="file" name="anexo[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                    <br>
                                    <div id="document-preview-container"></div>
                                    <div class="form-group">
                                        <?php
                                        if (!empty($documentos) && is_array($documentos)) {
                                            foreach ($documentos as $documento_atual) {
                                                if (is_string($documento_atual)) { // Adicionado verificação de tipo
                                                    echo '<div style="margin-bottom: 10px;">';
                                                    echo '<a class="documento-link" href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                    echo '</div>';
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                    <br>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-success" style="width:200px;height:50px;" onclick="exibirModalConfirmacao()"><span class="bi-pencil-square-fill"></span>&nbsp;Salvar Alterações</button>
                                </div>


                            </form>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function exibirModalConfirmacao() {
            var formData = new FormData(document.getElementById('formCadastro'));

            $.ajax({
                url: '/cartao/calcular_parcelamentos.php', // Mantemos a mesma URL para recalcular com os dados editados
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.erro) {
                        alert(response.mensagem);
                    } else {
                        // Preencher os dados da compra no modal
                        $('#modalConfirmacaoLabel').text('Confirmação de Edição'); // Alterar o título do modal
                        $('#modal-nome_cartao').text(response.compra.nome_cartao);
                        $('#modal-data_compra').text(response.compra.data_compra);
                        $('#modal-valor').text(response.compra.valor);
                        $('#modal-quantidade_parcelas').text(response.compra.quantidade_parcelas);
                        $('#modal-primeira_vencimento').text(response.compra.primeira_vencimento);
                        $('#modal-descricao').text(response.compra.descricao);

                        // Preencher os anexos (a lógica pode precisar ser ajustada dependendo de como você lida com a edição de anexos)
                        $('#modal-anexos').empty();
                        if (response.compra.anexos && response.compra.anexos.length > 0) {
                            response.compra.anexos.forEach(function(anexo) {
                                $('#modal-anexos').append('<li>' + anexo + '</li>');
                            });
                        } else if ($('#anexo').get(0).files.length > 0) {
                            // Se novos arquivos foram selecionados
                            var files = $('#anexo').get(0).files;
                            for (var i = 0; i < files.length; i++) {
                                $('#modal-anexos').append('<li>' + files[i].name + ' (Novo)</li>');
                            }
                        } else {
                            $('#modal-anexos').append('<li>Nenhum anexo</li>');
                        }

                        // Preencher a tabela de parcelamentos
                        var tbodyParcelas = $('#modal-parcelamentos');
                        tbodyParcelas.empty();
                        if (response.parcelamentos && response.parcelamentos.length > 0) { //verificação se existe parcelamento
                            response.parcelamentos.forEach(function(parcela) {
                                var row = '<tr>' +
                                    '<td style="text-align: center;">' + parcela.referencia_parcela + '</td>' +
                                    '<td style="text-align: center;">' + parcela.data_vencimento + '</td>' +
                                    '<td style="text-align: center;">' + parcela.valor_parcela_responsavel1 + '</td>' +
                                    '<td style="text-align: center;">' + parcela.valor_parcela_responsavel2 + '</td>' +
                                    '</tr>';
                                tbodyParcelas.append(row);
                            });
                        } else {
                            tbodyParcelas.append('<tr><td colspan="4">Não há parcelamentos a exibir</td></tr>');
                        }

                        // Exibir o modal
                        $('#modalConfirmacao').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição AJAX: " + error);
                    alert("Erro ao processar os dados para confirmação.");
                }
            });
        }

        function salvarCompraParcelada() {
            var formData = new FormData(document.getElementById('formCadastro'));

            $.ajax({
                url: '/cartao/atualizar_compras_parceladas.php', // Alterar para o script de atualização
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $('#modalConfirmacao').modal('hide');
                    alert(response.mensagem);
                    if (response.sucesso) {
                        window.location.href = '/cartao/lista_compras.php';
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao salvar os dados: " + error);
                    alert("Erro ao salvar a compra e os parcelamentos.");
                }
            });
        }



        function convertToUppercase() {
            var inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(function(input) {
                input.value = input.value.toUpperCase();
            });
        }

        function formatarValor(input) {
            var valor = input.value.replace(/\D/g, '');
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
            input.value = valor;
        }

        function addCurrencyPrefix() {
            var valorInput = document.getElementById('valor');
            if (valorInput.value && !valorInput.value.startsWith('R$')) {
                valorInput.value = 'R$ ' + valorInput.value;
            }
        }

        function previewDocuments(event) {
            var files = event.target.files;
            var container = document.getElementById('document-preview-container');
            container.innerHTML = '';
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var url = URL.createObjectURL(file);
                var iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.style.width = '300px';
                iframe.style.height = '400px';
                iframe.style.border = '1px solid #ddd';
                container.appendChild(iframe);
            }
        }
    </script>

    <div class="modal fade" id="modalConfirmacao" tabindex="-1" role="dialog" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacaoLabel">Confirmação</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Nome do Cartão:</strong> <span id="modal-nome_cartao"></span></p>
                    <p><strong>Data da Compra:</strong> <span id="modal-data_compra"></span></p>
                    <p><strong>Valor:</strong> <span id="modal-valor"></span></p>
                    <p><strong>Quantidade de Parcelas:</strong> <span id="modal-quantidade_parcelas"></span></p>
                    <p><strong>Primeira Data de Vencimento:</strong> <span id="modal-primeira_vencimento"></span></p>
                    <p><strong>Descrição:</strong> <span id="modal-descricao"></span></p>
                    <p><strong>Anexos:</strong>
                    <ul id="modal-anexos"></ul>
                    </p>
                    <h4>Parcelamentos:</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center;">PARCELA</th>
                                <th style="text-align: center;">VENCIMENTO</th>
                                <th style="text-align: center;">MAIARA CARLA</th>
                                <th style="text-align: center;">CARLA MARAISA</th>
                            </tr>
                        </thead>
                        <tbody id="modal-parcelamentos">
                        </tbody>
                    </table>

                    <p>Deseja salvar as alterações?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="salvarCompraParcelada()">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>