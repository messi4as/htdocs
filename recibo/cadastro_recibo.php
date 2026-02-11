<?php
session_start();
require 'db_connect.php';

// Consulta SQL para buscar todos os nomes de emitentes
$sql_emitente = "SELECT cod_emitente, nome_emitente FROM emitente";
$result_emitente = $conn->query($sql_emitente);

// Consulta SQL para buscar todos os nomes de emissores
$sql_emissor = "SELECT cod_emissor, nome_emissor FROM emissor";
$result_emissor = $conn->query($sql_emissor);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <title>CADASTRO DE RECIBOS</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        function abrirPopup(url) {
            window.open(url, 'popup', 'width=600,height=600,scrollbars=yes,resizable=yes');
            return false;
        }

        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <style>
        .form-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-between; }
        .form-group { display: flex; align-items: center; flex: 1; margin: 0 10px; }
        .form-label { font-weight: bold; margin-right: 10px; }
        input[type="text"], select { text-transform: none; width: 100%; }
        .table-container { width: 100%; overflow-x: auto; }
        #editor_descricao { height: 200px; margin-bottom: 20px; }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>CADASTRO DE RECIBOS
                            <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="cadastrar.php" method="post" id="reForm" onsubmit="convertToUppercase(); addCurrencyPrefix();">
                            
                            <div class="form-container">
                                <div class="form-group">
                                    <input type="text" id="codigo_emitente" name="cod_emitente" class="form-control" required readonly style="width:70px">
                                    <label class="form-label">&nbsp;PRESTADOR:</label>
                                    <select name="nome_emitente" id="nome_emitente" required class="form-control select2" onchange="updateEmitenteCodigo()">
                                        <option value="">Selecione um emitente</option>
                                        <?php
                                        if ($result_emitente->num_rows > 0) {
                                            while ($row_emitente = $result_emitente->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($row_emitente['cod_emitente']) . "'>" . htmlspecialchars($row_emitente['nome_emitente']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    &nbsp;<a href="cadastro_emitente.php" class="btn btn-primary" onclick="return abrirPopup(this.href);"><span class="bi-file-earmark-plus-fill"></span></a>
                                </div>
                            </div>

                            <br>

                            <div class="form-container">
                                <div class="form-group">
                                    <input type="text" id="codigo_emissor" name="cod_emissor" class="form-control" required readonly style="width:70px">
                                    <label class="form-label">&nbsp;SÓCIO/REP:</label>
                                    <select name="nome_emissor" id="nome_emissor" required class="form-control select2" onchange="updateEmissorCodigo()">
                                        <option value="">Selecione um emissor</option>
                                        <?php
                                        if ($result_emissor->num_rows > 0) {
                                            while ($row_emissor = $result_emissor->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($row_emissor['cod_emissor']) . "'>" . htmlspecialchars($row_emissor['nome_emissor']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    &nbsp;<a href="cadastro_emissor.php" class="btn btn-primary" onclick="return abrirPopup(this.href);"><span class="bi-file-earmark-plus-fill"></span></a>
                                </div>
                            </div>

                            <br>

                            <div class="form-container">
                                <div class="form-group">
                                    <label class="form-label">DATA:</label>
                                    <input type="date" name="data_recibo" required class="form-control" style="width:150px">
                                    <label class="form-label">&nbsp;LOCAL:</label>
                                    <input type="text" name="local_recibo" required class="form-control">
                                    <label class="form-label">&nbsp;VALOR:</label>
                                    <input type="text" id="valor_recibo" name="valor_recibo" required class="form-control" oninput="formatarValor(this); updateValorPorExtenso()">
                                </div>
                            </div>

                            <br>

                            <div class="form-group">
                                <label class="form-label">VALOR POR EXTENSO:</label>
                                <input type="text" id="valor_por_extenso" name="valor_ext_recibo" class="form-control" readonly>
                            </div>

                            <br>

                            <div class="form-group">
                                <label class="form-label">DESCRIÇÃO:</label>
                            </div>
                            
                            <div id="editor_descricao"></div>
                            <textarea name="descricao_recibo" id="descricao_recibo" style="display:none;"></textarea>

                            <div class="mt-3">
                                <button type="submit" name="cad_recibo" class="btn btn-success" style="width:200px;height:50px;">
                                    <span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inicialização do Quill
        var quillDescricao = new Quill('#editor_descricao', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'header': [1, 2, 3, false] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['clean']
                ]
            }
        });

        // Sincronização antes do envio
        var meuFormulario = document.getElementById('reForm');
        if (meuFormulario) {
            meuFormulario.addEventListener('submit', function() {
                document.getElementById('descricao_recibo').value = quillDescricao.root.innerHTML;
            });
        }

        function updateEmitenteCodigo() {
            document.getElementById('codigo_emitente').value = document.getElementById('nome_emitente').value;
        }

        function updateEmissorCodigo() {
            document.getElementById('codigo_emissor').value = document.getElementById('nome_emissor').value;
        }

        function formatarValor(input) {
            var valor = input.value.replace(/\D/g, '');
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
            input.value = valor;
        }

        function updateValorPorExtenso() {
            var valorInput = document.getElementById('valor_recibo').value;
            if (valorInput) {
                var valorNumerico = valorInput.replace(/[^\d,]/g, '').replace(',', '.');
                $.get('extenso.php', { valor_recibo: valorNumerico }, function(data) {
                    document.getElementById('valor_por_extenso').value = data.toUpperCase();
                });
            }
        }

        function addCurrencyPrefix() {
            var valorInput = document.getElementById('valor_recibo');
            if (valorInput.value && !valorInput.value.startsWith('R$')) {
                valorInput.value = 'R$ ' + valorInput.value;
            }
        }

        function convertToUppercase() {
            var inputs = document.querySelectorAll('input[type="text"]');
            inputs.forEach(function(input) {
                if(input.id !== 'valor_recibo') input.value = input.value.toUpperCase();
            });
        }
    </script>
</body>
</html>