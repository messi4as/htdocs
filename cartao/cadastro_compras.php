<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <title>CADASTRO DE COMPRAS NO CARTÃO</title>
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
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
        select,
        input[type="file"] {
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
                            <h4>CADASTRO DE COMPRAS
                                <a href="lista_compras.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formCadastro" enctype="multipart/form-data" onsubmit="return false;">
                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label" style="width:400px;">&nbsp;NOME DO CARTÃO:</label>
                                        <select name="nome_cartao" class="form-control" required>
                                            <option value="" disabled selected>Selecione o Cartão</option>
                                            <option value="CARTÃO M2">CARTÃO M2</option>
                                            <option value="CARTÃO MAIARA CARLA">CARTÃO MAIARA CARLA</option>
                                            <option value="CARTÃO CARLA MARAISA">CARTÃO CARLA MARAISA</option>
                                            <option value="CARTÃO BNDES">CARTÃO BNDES</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;DATA DA COMPRA:</label>
                                        <input type="date" name="data_compra" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;QUANTIDADE DE PARCELAS:</label>
                                        <input type="number" name="quantidade_parcelas" required class="form-control" min="1">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;VALOR:</label>
                                        <input id="valor" type="text" name="valor" required class="form-control" onblur="formatarValor(this)">
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
                                    <textarea name="descricao" class="form-control" style="height:150px;" onchange="convertToUppercase()"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">&nbsp;ANEXO:</label>
                                    <input id="anexo" type="file" name="anexo[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                    <br>
                                    <div id="document-preview-container"></div>
                                    <br>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-success" style="width:200px;height:50px;" onclick="exibirModalConfirmacao()"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmacao" tabindex="-1" role="dialog" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacaoLabel">Confirmação de Cadastro</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>Dados da Compra:</h4>
                    <p><strong>Cartão:</strong> <span id="modal-nome_cartao"></span></p>
                    <p><strong>Data da Compra:</strong> <span id="modal-data_compra"></span></p>
                    <p><strong>Valor Total:</strong> <span id="modal-valor"></span></p>
                    <p><strong>Parcelas:</strong> <span id="modal-quantidade_parcelas"></span></p>
                    <p><strong>Primeira Vencimento:</strong> <span id="modal-primeira_vencimento"></span></p>
                    <p><strong>Descrição:</strong> <span id="modal-descricao"></span></p>
                    <p><strong>Anexos:</strong> <ul id="modal-anexos"></ul></p>

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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarCompraParcelada()">Confirmar Cadastro</button>
                </div>
            </div>
        </div>
    </div>

    <script>
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
            input.value = valor; // Atualiza o campo com o valor formatado

            // Atualiza o atributo data-valor-decimal com o valor para salvar no banco
            var valorDecimal = input.value.replace(/\./g, '').replace(',', '.');
            input.setAttribute('data-valor-decimal', valorDecimal);
        }

        function exibirModalConfirmacao() {
            var formData = new FormData(document.getElementById('formCadastro'));
            var valorFormatado = document.getElementById('valor').value; // Pega o valor formatado
            var valorDecimal = document.getElementById('valor').getAttribute('data-valor-decimal'); // Pega o valor decimal


            $.ajax({
                url: '/cartao/calcular_parcelamentos.php',
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
                        $('#modal-nome_cartao').text(response.compra.nome_cartao);
                        $('#modal-data_compra').text(response.compra.data_compra);
                        $('#modal-valor').text(valorFormatado); // Exibe o valor formatado
                        $('#modal-quantidade_parcelas').text(response.compra.quantidade_parcelas);
                        $('#modal-primeira_vencimento').text(response.compra.primeira_vencimento);
                        $('#modal-descricao').text(response.compra.descricao);

                        // Preencher os anexos
                        $('#modal-anexos').empty();
                        if (response.compra.anexos && response.compra.anexos.length > 0) {
                            response.compra.anexos.forEach(function(anexo) {
                                $('#modal-anexos').append('<li>' + anexo + '</li>');
                            });
                        } else {
                            $('#modal-anexos').append('<li>Nenhum anexo</li>');
                        }

                        // Preencher a tabela de parcelamentos
                        var tbodyParcelas = $('#modal-parcelamentos');
                        tbodyParcelas.empty();
                        response.parcelamentos.forEach(function(parcela) {
                            var row = '<tr>' +
                                '<td style="text-align: center;">' + parcela.referencia_parcela + '</td>' +
                                '<td style="text-align: center;">' + parcela.data_vencimento + '</td>' +
                                '<td style="text-align: center;">' + parcela.valor_parcela_responsavel1 + '</td>' +
                                '<td style="text-align: center;">' + parcela.valor_parcela_responsavel2 + '</td>' +
                                '</tr>';
                            tbodyParcelas.append(row);
                        });

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
             var valorDecimal = document.getElementById('valor').getAttribute('data-valor-decimal');  // Pega o valor decimal do atributo

            // Adiciona o valor decimal ao formData
            formData.append('valor_decimal', valorDecimal);

            $.ajax({
                url: '/cartao/salvar_compras_parceladas.php',
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
    </script>
</body>
</html>
