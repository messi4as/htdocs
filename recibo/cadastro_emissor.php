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
    <link href="css/docs.css" rel="stylesheet">
    <title>EMISSOR</title>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>EMISSORES CADASTRADOS
                                <a href="lista_emissor.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="convertToUppercase(); addCurrencyPrefix();">
                                <style>
                                    .form-container {
                                        display: flex;
                                        flex-wrap: wrap;
                                        gap: 20px;
                                    }

                                    .form-group {
                                        display: flex;
                                        align-items: center;
                                    }

                                    .form-label {
                                        font-weight: bold;
                                        margin-right: 10px;
                                    }

                                    input[type="text"],
                                    textarea {
                                        text-transform: uppercase;
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
                                <div>
                                    <label class="form-label">NOME</label>
                                    <input type="text" name="nome_emissor" required class="form-control">
                                    <br>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">CPF:</label>
                                            <input id="cpf" type="text" name="cpf_emissor" class="form-control" style="width:150px">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">CNPJ:</label>
                                            <input id="cnpj" type="text" name="cnpj_emissor" class="form-control" style="width:200px">
                                        </div>

                                    </div>
                                    <br>
                                    <div>
                                        <label><strong>ENDEREÇO: </label></strong>
                                        <input type="text" name="endereco_emissor" class="form-control">
                                    </div>
                                    <div>
                                        <label><strong>BAIRRO: </label></strong>
                                        <input type="text" name="bairro_emissor" class="form-control" style="width:500px">
                                    </div>
                                    <br>

                                    <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                    <input id="documentos" type="file" name="documentos_emissores[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                    <br>
                                    <div id="document-preview-container"></div>
                                    <br>


                                    <p>
                                    <div>
                                        <p>
                                            <button type="submit" name="cad_emissor" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                    <script src="js/jquery-3.7.1.slim.min.js"></script>
                    <script src="js/jquery.mask.min.js"></script>
                    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#cpf').mask('000.000.000-00', {
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