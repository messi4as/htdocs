<?php
session_start();
require 'db_connect.php';
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <title>CADASTRO DE EMITENTE</title>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
    <script>
        $(document).ready(function() {
            $('#tipo_chave_pix').change(function() {
                var tipo = $(this).val();
                var $chavePix = $('input[name="chave_pix_emitente"]');

                $chavePix.val(''); // Limpa o campo

                if (tipo === 'celular') {
                    $chavePix.inputmask('(99) 9 9999-9999');
                } else if (tipo === 'cpf') {
                    $chavePix.inputmask('999.999.999-99');
                } else if (tipo === 'email') {
                    $chavePix.inputmask({
                        alias: "email"
                    });
                } else if (tipo === 'cnpj') {
                    $chavePix.inputmask('99.999.999/9999-99');
                } else {
                    $chavePix.inputmask('remove'); // Remove a máscara para o tipo aleatório
                }
            });
        });
    </script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>CADASTRO DE EMITENTE
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="convertToUppercase(); addCurrencyPrefix();">
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
                                        <label class="form-label">NOME</label>
                                        <input type="text" name="nome_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;NACIONALIDADE:</label>
                                        <input type="text" name="nacionalidade_emitente" required class="form-control">
                                    </div>
                                </div>
                                <br>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label">CPF:</label>
                                        <input id="cpf" type="text" name="cpf_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;RG_/_ORGÃO:</label>
                                        <input type="text" name="rg_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;ESTADO_CIVIL:</label>
                                        <input type="text" name="estado_civil_emitente" required class="form-control">
                                    </div>
                                </div>
                                <br>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label">NOME_EMPRESARIAL: </label>
                                        <input type="text" name="nome_empresa" class="form-control">

                                        <label class="form-label">&nbsp;CNPJ:</label>
                                        <input id="cnpj" type="text" name="cnpj_emitente" class="form-control" style="width:200px">
                                    </div>
                                </div>
                                <br>

                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label">ENDEREÇO: </label>
                                        <input type="text" name="endereco_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;BAIRRO: </label>
                                        <input type="text" name="bairro_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;CEP: </label>
                                        <input id="cep" type="text" name="cep_emitente" required class="form-control">
                                    </div>
                                </div>
                                <br>

                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label">BANCO:</label>
                                        <input id="cnpj" type="text" name="banco_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;AGÊNCIA:</label>
                                        <input type="text" name="agencia_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;CONTA:</label>
                                        <input type="text" name="conta_emitente" required class="form-control">
                                    </div>
                                </div>
                                <br>

                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label">TIPO_CHAVE_PIX:</label>
                                        <select id="tipo_chave_pix" name="tipo_chave_pix_emitente" required class="form-control">
                                            <option value="">Selecione o tipo de chave</option>
                                            <option value="celular">CELULAR</option>
                                            <option value="cpf">CPF</option>
                                            <option value="email">E-MAIL</option>
                                            <option value="cnpj">CNPJ</option>
                                            <option value="aleatorio">ALEATÓRIA</option>
                                        </select>

                                        <label class="form-label">&nbsp;CHAVE_PIX:</label>
                                        <input type="text" name="chave_pix_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;FAVORECIDO: </label>
                                        <input type="text" name="favorecido_emitente" required class="form-control">

                                        <label class="form-label">&nbsp;CPF_FAVORECIDO: </label>
                                        <input id="cpf_favorecido" type="text" name="cpf_favorecido_emitente" required class="form-control">
                                    </div>
                                </div>
                                <br>

                                <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                <input id="documentos" type="file" name="documentos_emitentes[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                <br>
                                <div id="document-preview-container"></div>
                                <br>


                                <p>
                                <div>
                                    <button type="submit" name="cad_emitente" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
                                </div>
                            </form>
                        </div>
                    </div>



                    <script>
                        $(document).ready(function() {
                            $('#cpf').mask('000.000.000-00', {
                                reverse: true
                            });
                            $('#cpf_favorecido').mask('000.000.000-00', {
                                reverse: true
                            });
                            $('#cnpj').mask('00.000.000/0000-00', {
                                reverse: true
                            });
                            $('#celular').mask('(00) 0 0000-0000');
                            $('#fixo').mask('(00) 0000-0000');
                            $('#cep').mask('00.000-000');
                            $('#valor').mask('000.000.000.000.000,00', {
                                reverse: true
                            });
                        });

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
                    </script>

</body>

</html>