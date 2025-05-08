<?php
session_start();
require 'db_connect.php';

// Consulta SQL para buscar todos os nomes de emitentes
$sql_emitente = "SELECT cod_emitente, nome_emitente FROM emitente";
$result_emitente = $conn->query($sql_emitente);

// Consulta SQL para buscar todos os nomes de emissores
$sql_emissor = "SELECT cod_emissor, nome_emissor FROM emissor";
$result_emissor = $conn->query($sql_emissor);

// Recupera o recibo a ser editado
if (isset($_GET['id'])) {
    $codigo_recibo = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM recibo WHERE cod_recibo='$codigo_recibo'";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        $recibo = mysqli_fetch_array($query);
        $descricao = str_replace('<br>', "\n", $recibo['descricao_recibo']);
        $codigo_emitente_salvo = $recibo['cod_emitente'];
        $codigo_emissor_salvo = $recibo['cod_emissor'];
    } else {
        echo "<h5>Nenhum Recibo encontrado</h5>";
        exit;
    }
} else {
    echo "<h5>ID do Recibo não fornecido</h5>";
    exit;
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">    
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function abrirPopup(url) {
            window.open(url, 'popup', 'width=600,height=600,scrollbars=yes,resizable=yes');
            return false;
        }

        $(document).ready(function() {
            $('.select2').select2();
        });

        function updateEmitenteCodigo() {
            var select = document.getElementById("nome_emitente");
            var selectedOption = select.options[select.selectedIndex];
            var codigoEmitente = selectedOption.getAttribute("data-cod-emitente");
            document.getElementById("codigo_emitente").value = codigoEmitente;
        }

        function updateEmissorCodigo() {
            var emissorSelect = document.getElementById('nome_emissor');
            var codigoEmissorInput = document.getElementById('codigo_emissor');
            codigoEmissorInput.value = emissorSelect.value;
        }
    </script>

<title>EDITAR RECIBOS</title>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>EDITAR RECIBOS
                                <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">

                            <?php
                            if (isset($_GET['id'])) {
                                $codigo_recibo = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM recibo, emitente, emissor WHERE cod_recibo='$codigo_recibo' 
                            and recibo.cod_emitente = emitente.cod_emitente and recibo.cod_emissor = emissor.cod_emissor";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $codigo_recibo = mysqli_fetch_array($query);
                                    $descricao = str_replace('<br>', "\n", $codigo_recibo['descricao_recibo']);

                                    // Formata o código do recibo
                                    $codigo = str_pad($codigo_recibo['cod_recibo'], 4, '0', STR_PAD_LEFT);
                                    $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);

                            ?>
                                    <form action="cadastrar.php" method="post" onsubmit="addCurrencyPrefix();">
                                        <input type="hidden" name="id_recibo" value="<?= $codigo_recibo['cod_recibo'] ?>">
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

                                        <div class="form-group">
                                            <label class="form-label">CÓDIGO DO RECIBO</label>
                                            <a class="form-control" style="width:75px ; background-color: #e9ecef;"><?= $cod_formatado ?></a>
                                        </div>
                                        <br>
                                        <!-- Campo Emitente -->
                                        <div class="form-container">
                                            <div class="form-group">
                                                <!-- <label class="form-label">CÓDIGO DO EMITENTE</label> -->
                                                <input type="text" id="codigo_emitente" name="cod_emitente" class="form-control" value="<?= htmlspecialchars($codigo_emitente_salvo) ?>" required readonly style="width:70px">
                                                <label class="form-label">&nbsp;PRESTADOR_DE_SERVIÇO:</label>
                                                <select name="nome_emitente" id="nome_emitente" required class="form-control select2" onchange="updateEmitenteCodigo()" style="width:650px">>
                                                    <option value="">Selecione um emitente</option>
                                                    <?php
                                                    if ($result_emitente->num_rows > 0) {
                                                        while ($row_emitente = $result_emitente->fetch_assoc()) {
                                                            $selected = $row_emitente['cod_emitente'] == $codigo_emitente_salvo ? 'selected' : '';
                                                            echo "<option value='" . htmlspecialchars($row_emitente['cod_emitente']) . "' data-cod-emitente='" . htmlspecialchars($row_emitente['cod_emitente']) . "' $selected>" . htmlspecialchars($row_emitente['nome_emitente']) . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>Nenhum emitente encontrado</option>";
                                                    }
                                                    ?>
                                                </select>
                                                &nbsp;<a href="cadastro_emitente.php" class="btn btn-primary float-end" onclick="return abrirPopup(this.href);">
                                                    <span class="bi-file-earmark-plus-fill"></span>&nbsp;Adicionar
                                                </a>
                                            </div>
                                        </div>
                                        <br>
                                        <!-- Campo Emissor -->
                                        <div class="form-container">
                                            <div class="form-group">
                                                <!-- <label class="form-label">CÓDIGO DO EMISSOR</label> -->
                                                <input type="text" id="codigo_emissor" name="cod_emissor" class="form-control" value="<?= $codigo_recibo['cod_emissor'] ?>" required readonly style="width:70px">
                                                <label class="form-label">&nbsp;SÓCIO_/_REPRESENTANTE:</label>
                                                <select name="nome_emissor" id="nome_emissor" required class="form-control select2" onchange="updateEmissorCodigo()" style="width:640px">>
                                                    <option value="">Selecione um emissor</option>
                                                    <?php
                                                    if ($result_emissor->num_rows > 0) {
                                                        while ($row_emissor = $result_emissor->fetch_assoc()) {
                                                            $selected = $row_emissor['cod_emissor'] == $codigo_emissor_salvo ? 'selected' : '';
                                                            echo "<option value='" . htmlspecialchars($row_emissor['cod_emissor']) . "' $selected>" . htmlspecialchars($row_emissor['nome_emissor']) . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>Nenhum emissor encontrado</option>";
                                                    }
                                                    ?>
                                                </select>
                                                &nbsp;<a href="cadastro_emissor.php" class="btn btn-primary float-end" onclick="return abrirPopup(this.href);">
                                                    <span class="bi-file-earmark-plus-fill"></span>&nbsp;Adicionar
                                                </a>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">DATA:</label>
                                                <input type="date" name="data_recibo" value="<?= $codigo_recibo['data_recibo'] ?>" required class="form-control" style="width:200px">

                                                <label class="form-label">&nbsp;LOCAL: </label>
                                                <input type="text" name="local_recibo" value="<?= $codigo_recibo['local_recibo'] ?>" required class="form-control" style="width:400px">

                                                <label class="form-label">&nbsp;VALOR:</label>
                                                <input type="text" id="valor_recibo" name="valor_recibo" value="<?= $codigo_recibo['valor_recibo'] ?>" required class="form-control" oninput="formatarValor(this); updateValorPorExtenso()" style="width:190px">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label class="form-label">VALOR_POR_EXTENSO:</label>
                                            <input type="text" id="valor_por_extenso" name="valor_ext_recibo" value="<?= $codigo_recibo['valor_ext_recibo'] ?>" class="form-control" readonly style="width:800px">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                                <label class="form-label">DESCRIÇÃO:</label>

                                            </div>

                                        </div>
                                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                                            <textarea name="descricao_recibo" class="form-control" style="height:150px ;width:990px" id="descricao"><?= htmlspecialchars($descricao) ?></textarea>
                                        </div>
                                        <p>
                                        <div>
                                            <p>
                                                <button type="submit" name="edit_recibo" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button>
                                                <button type="submit" name="cad_recibo" class="btn btn-primary" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Duplicar</button>

                                        </div>
                        </div>
                        </form>
                    </div>
                </div>


        <?php
                                } else {
                                    echo "<h5>Nenhum Recibo encontrado</h5>";
                                }
                            }
        ?>

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


            });

            function addCurrencyPrefix() {
                var valorInput = document.getElementById('valor_recibo');
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



            function formatarValor(input) {
                var valor = input.value.replace(/\D/g, '');
                valor = (valor / 100).toFixed(2) + '';
                valor = valor.replace(".", ",");
                valor = valor.replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
                input.value = valor;
            }

            function updateValorPorExtenso() {
                var valorInput = document.getElementById('valor_recibo').value;
                var valorPorExtensoInput = document.getElementById('valor_por_extenso');

                if (valorInput) {
                    // Substitui todos os caracteres que não são dígitos ou vírgula
                    var valorNumerico = valorInput.replace(/[^\d,]/g, '').replace(',', '.');
                    console.log("Valor numérico após substituição:", valorNumerico); // Log do valor numérico após substituição

                    // Verifica se o valor numérico é válido
                    if (!isNaN(valorNumerico)) {
                        // Faz uma chamada AJAX para o PHP
                        $.get('extenso.php', {
                            valor_recibo: valorNumerico
                        }, function(data) {
                            console.log("Resposta do extenso.php:", data); // Log da resposta do extenso.php
                            valorPorExtensoInput.value = data;
                        }).fail(function() {
                            console.error("Erro ao chamar extenso.php");
                        });
                    } else {
                        valorPorExtensoInput.value = '';
                    }
                } else {
                    valorPorExtensoInput.value = '';
                }
            }
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