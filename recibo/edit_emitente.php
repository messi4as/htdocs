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

    <title>VISUALIZAR PRESTADOR DE SERVIÇO</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
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
        <?php include('mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>VISUALIZAR PRESTADOR DE SERVIÇO
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">

                            <?php
                            if (isset($_GET['id'])) {
                                $codigo_emitente = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM emitente WHERE cod_emitente='$codigo_emitente' ";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $codigo_emitente = mysqli_fetch_array($query);

                                    // Formata o código do recibo
                                    $codigo = str_pad($codigo_emitente['cod_emitente'], 4, '0', STR_PAD_LEFT);
                                    $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);

                                    if (isset($codigo_emitente['documentos_emitente'])) {
                                        $documentos_emitentes = json_decode($codigo_emitente['documentos_emitente'], true);
                                    } else {
                                        $documentos_emitentes = []; // Defina um array vazio ou um valor padrão
                                    }

                            ?>

                                    <script>
                                        function excluirDocumentoEmitente(caminho) {
                                            if (confirm('Tem certeza que deseja excluir este documento?')) {
                                                var form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = 'excluir_documento_emitente.php';

                                                var inputCaminho = document.createElement('input');
                                                inputCaminho.type = 'hidden';
                                                inputCaminho.name = 'caminho_documento';
                                                inputCaminho.value = caminho;

                                                var inputIdEmitente = document.createElement('input');
                                                inputIdEmitente.type = 'hidden';
                                                inputIdEmitente.name = 'id_emitente';
                                                inputIdEmitente.value = '<?= $codigo_emitente['cod_emitente'] ?>';

                                                var inputDocumentosAtuais = document.createElement('input');
                                                inputDocumentosAtuais.type = 'hidden';
                                                inputDocumentosAtuais.name = 'documentos_atuais';
                                                inputDocumentosAtuais.value = '<?= json_encode($documentos_emitentes) ?>';

                                                form.appendChild(inputCaminho);
                                                form.appendChild(inputIdEmitente);
                                                form.appendChild(inputDocumentosAtuais);
                                                document.body.appendChild(form);
                                                form.submit();
                                            }
                                        }
                                    </script>

                                    <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="convertToUppercase(); addCurrencyPrefix();">
                                        <input type="hidden" name="id_emitente" value="<?= $codigo_emitente['cod_emitente'] ?>">
                                        <input type="hidden" name="documentos_atuais" value='<?php echo json_encode($documentos_emitentes); ?>'>


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

                                            .documento-item {
                                                margin-bottom: 5px;
                                            }

                                            .documento-link {
                                                margin-right: 10px;
                                            }

                                            .documento-botao {
                                                width: 80px;
                                                height: 40px;
                                            }
                                        </style>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">NOME</label>
                                                <input type="text" name="nome_emitente" value="<?= $codigo_emitente['nome_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;NACIONALIDADE:</label>
                                                <input type="text" name="nacionalidade_emitente" value="<?= $codigo_emitente['nacionalidade_emitente'] ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">CPF:</label>
                                                <input id="cpf" type="text" name="cpf_emitente" value="<?= $codigo_emitente['cpf_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;RG_/_ORGÃO:</label>
                                                <input type="text" name="rg_emitente" value="<?= $codigo_emitente['rg_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;ESTADO_CIVIL:</label>
                                                <input type="text" name="estado_civil_emitente" value="<?= $codigo_emitente['estado_civil_emitente'] ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">NOME_EMPRESARIAL: </label>
                                                <input type="text" name="nome_empresa" value="<?= $codigo_emitente['nome_empresa'] ?>" class="form-control">

                                                <label class="form-label">&nbsp;CNPJ:</label>
                                                <input id="cnpj" type="text" name="cnpj_emitente" value="<?= $codigo_emitente['cnpj_emitente'] ?>" class="form-control" style="width:200px">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">ENDEREÇO: </label>
                                                <input type="text" name="endereco_emitente" value="<?= $codigo_emitente['endereco_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;BAIRRO: </label>
                                                <input type="text" name="bairro_emitente" value="<?= $codigo_emitente['bairro_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;CEP: </label>
                                                <input id="cep" type="text" name="cep_emitente" value="<?= $codigo_emitente['cep_emitente'] ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">BANCO:</label>
                                                <input id="cnpj" type="text" name="banco_emitente" value="<?= $codigo_emitente['banco_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;AGÊNCIA:</label>
                                                <input type="text" name="agencia_emitente" value="<?= $codigo_emitente['agencia_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;CONTA:</label>
                                                <input type="text" name="conta_emitente" value="<?= $codigo_emitente['conta_emitente'] ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>


                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">TIPO_CHAVE_PIX:</label>
                                                <select id="tipo_chave_pix" name="tipo_chave_pix_emitente" required class="form-control">
                                                    <option value="">Selecione o tipo de chave</option>
                                                    <option value="celular" <?= $codigo_emitente['tipo_chave_pix_emitente'] == 'CELULAR' ? 'selected' : '' ?>>CELULAR</option>
                                                    <option value="cpf" <?= $codigo_emitente['tipo_chave_pix_emitente'] == 'CPF' ? 'selected' : '' ?>>CPF</option>
                                                    <option value="email" <?= $codigo_emitente['tipo_chave_pix_emitente'] == 'E-MAIL' ? 'selected' : '' ?>>E-MAIL</option>
                                                    <option value="cnpj" <?= $codigo_emitente['tipo_chave_pix_emitente'] == 'CNPJ' ? 'selected' : '' ?>>CNPJ</option>
                                                    <option value="aleatorio" <?= $codigo_emitente['tipo_chave_pix_emitente'] == 'ALEATÓRIA' ? 'selected' : '' ?>>ALEATÓRIA</option>
                                                </select>


                                                <label class="form-label">&nbsp;CHAVE_PIX:</label>
                                                <input type="text" name="chave_pix_emitente" value="<?= $codigo_emitente['chave_pix_emitente'] ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">

                                                <label class="form-label">&nbsp;FAVORECIDO: </label>
                                                <input type="text" name="favorecido_emitente" value="<?= $codigo_emitente['favorecido_emitente'] ?>" required class="form-control">

                                                <label class="form-label">&nbsp;CPF_FAVORECIDO: </label>
                                                <input id="cpf_favorecido" type="text" name="cpf_favorecido_emitente" value="<?= $codigo_emitente['cpf_favorecido_emitente'] ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>

                                        <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                        <input id="documentos" type="file" name="documentos_emitente[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                        <br>

                                        <div id="document-preview-container">



                                            <?php
                                            // Supondo que $documentos_emitentes contenha os caminhos dos documentos atuais
                                            if (!empty($documentos_emitentes) && is_array($documentos_emitentes)) {
                                                foreach ($documentos_emitentes as $documento_atual) {
                                                    echo '<div style="margin-bottom: 10px;">';
                                                    echo '<a href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                   // echo ' <button class="btn btn-danger documento-botao" type="button" onclick="excluirDocumentoEmitente(\'' . $documento_atual . '\')">Excluir</button>';
                                                    echo '</div>';
                                                }
                                            }
                                            ?>

                                            <?php if (!empty($documentos_emitentes)): ?>
                                                <?php foreach ($documentos_emitentes as $documento): ?>
                                                    <?php
                                                    $documento_url = '/recibo/uploads/documentos/' . rawurlencode(basename($documento));
                                                    ?>
                                                    <!-- <iframe src="<?= $documento_url ?>" style="width:300px; height:400px; border:1px solid #ddd;"></iframe> -->
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p>Nenhum documento disponível para visualização.</p>
                                            <?php endif; ?>
                                        </div>
                                        <br>



                                        <div>
                                            <button type="submit" name="edit_emitente" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button>
                                        </div>
                                    </form>
                        </div>
                    </div>

            <?php
                                } else {
                                    echo "<h5>Nenhum Emitente encontrado</h5>";
                                }
                            }
            ?>



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

</body>

</html>