<?php
session_start();
require 'db_connect.php';
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <title>Editar Ordem de Serviço</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="/js/jquery-3.7.1.slim.min.js"></script>
    <script src="/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>EDITAR ORDEM DE SERVIÇO
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['id'])) {
                                $os_codigo = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM ordem_servico WHERE codigo='$os_codigo'";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $os_codigo = mysqli_fetch_array($query);
                                    $descricao = str_replace('<br>', "\n", $os_codigo['descricao']);
                                    $forma_pagamento = str_replace('<br>', "\n", $os_codigo['forma_pagamento']);


                                    // Formata o código do recibo
                                    $codigo = str_pad($os_codigo['codigo'], 4, '0', STR_PAD_LEFT);
                                    $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);

                                    // Converte as quebras de linha para exibição
                                    $descricao_exibir = nl2br(htmlspecialchars($descricao));
                                    $forma_pagamento_exibir = nl2br(htmlspecialchars($forma_pagamento));

                            ?>
                                    <form action="cadastrar.php" method="post" onsubmit="addCurrencyPrefix();">
                                        <input type="hidden" name="os_id" value="<?= $os_codigo['codigo'] ?>">

                                        <style>
                                            .form-container {
                                                display: flex;
                                                flex-wrap: wrap;
                                                gap: 20px;
                                                justify-content: space-between;
                                                /* Adicionado */
                                            }

                                            .form-group {
                                                display: flex;
                                                align-items: center;
                                                flex: 1;
                                                /* Adicionado */
                                                margin: 0 10px;
                                                /* Adicionado */
                                            }

                                            .form-label {
                                                font-weight: bold;
                                                margin-right: 10px;
                                            }

                                            input[type="text"],
                                            textarea,
                                            select {
                                                text-transform: none;
                                                width: 100%;
                                                /* Adicionado */
                                            }

                                            .btn {
                                                flex: 0 0 auto;
                                                /* Adicionado */
                                                margin-left: 10px;
                                                /* Adicionado */
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
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">CÓDIGO</label>
                                                <a class="form-control" style="width:75px ; background-color: #e9ecef;"><?= $cod_formatado ?></a>
                                                <label class="form-label">&nbsp;NOME</label>
                                                <input type="text" name="nome" value="<?= $os_codigo['nome'] ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">CPF:</label>
                                                <input id="cpf" type="text" name="cpf" value="<?= $os_codigo['cpf'] ?>" class="form-control">

                                                <label class="form-label">&nbsp;CNPJ:</label>
                                                <input id="cnpj" type="text" name="cnpj" value="<?= $os_codigo['cnpj'] ?>" class="form-control">

                                                <label class="form-label">&nbsp;CELULAR:</label>
                                                <input id="celular" type="text" name="celular" value="<?= $os_codigo['celular'] ?>" class="form-control">

                                                <label class="form-label">&nbsp;TELEFONE_FIXO:</label>
                                                <input id="fixo" type="text" name="telefone_fixo" value="<?= $os_codigo['telefone_fixo'] ?>" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div>
                                            <label><strong>ENDEREÇO: </label></strong>
                                            <input type="text" name="endereco" value="<?= $os_codigo['endereco'] ?>" class="form-control">
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">CIDADE:</label>
                                                <input type="text" name="cidade" value="<?= $os_codigo['cidade'] ?>" class="form-control">

                                                <label class="form-label">&nbsp;CEP:</label>
                                                <input id="cep" type="text" name="cep" value="<?= $os_codigo['cep'] ?>" class="form-control">

                                                <label class="form-label">&nbsp;DATA:</label>
                                                <input type="date" name="data" required value="<?= $os_codigo['data'] ?>" class="form-control">

                                                <label class="form-label">&nbsp;VALOR:</label>
                                                <input id="valor" type="text" name="valor" required value="<?= $os_codigo['valor'] ?>" class="form-control" oninput="formatarValor(this)">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                            <label><strong>DESCRIÇÃO:&emsp;</label></strong>
                                            <textarea name="descricao" class="form-control" style="height:150px;" id="descricao"><?= $descricao_exibir ?></textarea>
                                            <label><strong>FORMA DE PAGAMENTO:&emsp;</label></strong>
                                            <textarea name="forma_pagamento" class="form-control" style="height:150px;" id="forma_pagamento"><?= $forma_pagamento_exibir ?></textarea>
                                        </div>
                                        <p>
                                        <div>
                                            <p>
                                                <button type="submit" name="edit_os" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-pencil-fill"></span>&nbsp;Salvar</button>
                                                <button type="submit" name="create_os" class="btn btn-primary" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Duplicar</button>
                                        </div>
                                    </form>
                            <?php
                                } else {
                                    echo "<h5>Ordem de Serviço não encontrada</h5>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#cpf').mask('000.000.000-00', {
            reverse: true
        });
        $('#cnpj').mask('00.000.000/0000-00', {
            reverse: true
        });
        $('#celular').mask('(00) 0 0000-0000');
        $('#fixo').mask('(00) 0000-0000');
        $('#cep').mask('00.000-000');
    </script>
    <script>
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

        function convertToUppercase() {
            var inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(function(input) {
                input.value = input.value.toUpperCase();
            });
        }

        // Evitar envio do formulário ao pressionar Enter no textarea
        document.getElementById('descricao').addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                var textarea = event.target;
                var start = textarea.selectionStart;
                var end = textarea.selectionEnd;
                textarea.value = textarea.value.substring(0, start) + "\n" + textarea.value.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + 1;
            }
        });

        document.getElementById('forma_pagamento').addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                var textarea = event.target;
                var start = textarea.selectionStart;
                var end = textarea.selectionEnd;
                textarea.value = textarea.value.substring(0, start) + "\n" + textarea.value.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + 1;
            }
        });
    </script>

    </script>
    <script type="text/javascript">
        bkLib.onDomLoaded(function() {
            nicEditors.allTextAreas()
        }); // convert all text areas to rich text editor on that page
        bkLib.onDomLoaded(function() {
            new nicEditor().panelInstance('area1');
        }); // convert text area with id area1 to rich text editor.
        bkLib.onDomLoaded(function() {
            new nicEditor({
                fullPanel: true
            }).panelInstance('area2');
        }); // convert text area with id area2 to rich text editor with full panel.
    </script>
</body>

</html>