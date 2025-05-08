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

    <title>VISUALIZAR SÓCIO / REPRESENTANTE</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
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
                            <h4>VISUALIZAR SÓCIO / REPRESENTANTE
                                <a href="lista_emissor.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">

                            <?php
                            if (isset($_GET['id'])) {
                                $codigo_emissor = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM emissor WHERE cod_emissor='$codigo_emissor' ";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $codigo_emissor = mysqli_fetch_array($query);

                                    // Formata o código do Emissor
                                    $codigo = str_pad($codigo_emissor['cod_emissor'], 4, '0', STR_PAD_LEFT);
                                    $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);

                                    if (isset($codigo_emissor['documentos_emissor'])) {
                                        $documentos_emissores = json_decode($codigo_emissor['documentos_emissor'], true);
                                    } else {
                                        $documentos_emissores = []; // Defina um array vazio ou um valor padrão
                                    }



                            ?>

                                    <script>
                                        function excluirDocumentoEmissor(caminho) {
                                            if (confirm('Tem certeza que deseja excluir este documento?')) {
                                                var form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = 'excluir_documento_emissor.php';

                                                var inputCaminho = document.createElement('input');
                                                inputCaminho.type = 'hidden';
                                                inputCaminho.name = 'caminho_documento';
                                                inputCaminho.value = caminho;

                                                var inputIdEmissor = document.createElement('input');
                                                inputIdEmissor.type = 'hidden';
                                                inputIdEmissor.name = 'id_emissor';
                                                inputIdEmissor.value = '<?= $codigo_emissor['cod_emissor'] ?>';

                                                var inputDocumentosAtuais = document.createElement('input');
                                                inputDocumentosAtuais.type = 'hidden';
                                                inputDocumentosAtuais.name = 'documentos_atuais';
                                                inputDocumentosAtuais.value = '<?= json_encode($documentos_emissores) ?>';

                                                form.appendChild(inputCaminho);
                                                form.appendChild(inputIdEmissor);
                                                form.appendChild(inputDocumentosAtuais);
                                                document.body.appendChild(form);
                                                form.submit();
                                            }
                                        }
                                    </script>




                                    <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="convertToUppercase(); addCurrencyPrefix();">
                                        <input type="hidden" name="id_emissor" value="<?= $codigo_emissor['cod_emissor'] ?>">
                                        <input type="hidden" name="documentos_atuais" value='<?php echo json_encode($documentos_emissores); ?>'>



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
                                                margin-bottom: 10px;
                                            }

                                            .documento-link {
                                                margin-right: 10px;
                                            }

                                            .documento-botao {
                                                width: 80px;
                                                height: 40px;
                                            }
                                        </style>

                                        <div>
                                            <label class="form-label">NOME</label>
                                            <input type="text" name="nome_emissor" value="<?= $codigo_emissor['nome_emissor'] ?>" required class="form-control">
                                            <br>
                                            <div class="form-container">
                                                <div class="form-group">
                                                    <label class="form-label">CPF:</label>
                                                    <input id="cpf" type="text" name="cpf_emissor" value="<?= $codigo_emissor['cpf_emissor'] ?>" class="form-control" style="width:150px">

                                                    <label class="form-label">&nbsp;CNPJ:</label>
                                                    <input id="cnpj" type="text" name="cnpj_emissor" value="<?= $codigo_emissor['cnpj_emissor'] ?>" class="form-control" style="width:200px">
                                                </div>
                                            </div>
                                            <br>
                                            <div>
                                                <label class="form-label">ENDEREÇO: </label>
                                                <input type="text" name="endereco_emissor" value="<?= $codigo_emissor['endereco_emissor'] ?>" class="form-control">
                                            </div>
                                            <div>
                                                <label class="form-label">BAIRRO: </label></strong>
                                                <input type="text" name="bairro_emissor" value="<?= $codigo_emissor['bairro_emissor'] ?>" class="form-control" style="width:500px">
                                            </div>
                                            <br>


                                            <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                            <input id="documentos" type="file" name="documentos_emissor[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                            <br>

                                        

                                                <?php
                                                // Supondo que $documentos_emissores contenha os caminhos dos documentos atuais
                                                if (!empty($documentos_emissores) && is_array($documentos_emissores)) {
                                                    foreach ($documentos_emissores as $documento_atual) {
                                                        echo '<div style="margin-bottom: 10px;">';
                                                        echo '<a href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                       // echo ' <button class="btn btn-danger documento-botao" type="button" onclick="excluirDocumentoEmissor(\'' . $documento_atual . '\')">Excluir</button>';
                                                        echo '</div>';
                                                    }
                                                }
                                                ?>
                                                <br>



                                                <div>

                                                    <button type="submit" name="edit_emissor" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button>
                                                </div>
                                            </div>



                                    </form>
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