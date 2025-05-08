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
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="path/to/twain-web-capture.js"></script>
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

    <title>EDITAR PAGAMENTO</title>

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

                            <h4>EDITAR PAGAMENTO
                                <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4> 
                        </div>

                        <div class="card-body">

                            <?php
                            if (isset($_GET['id'])) {
                                $id_financeiro = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM financeiro WHERE cod_financeiro='$id_financeiro'";
                                $query = mysqli_query($conn, $sql);




                                if (mysqli_num_rows($query) > 0) {
                                    $financeiro = mysqli_fetch_array($query);
                                    $descricao = str_replace('<br>', "\n", $financeiro['descricao']);
                                    $forma_pagamento = str_replace('<br>', "\n", $financeiro['forma_pagamento']);


                                    if (isset($financeiro['documento'])) {
                                        $documentos_pagamentos = json_decode($financeiro['documento'], true);
                                    } else {
                                        $documentos_pagamentos = []; // Defina um array vazio ou um valor padrão
                                    }
                                    if (isset($financeiro['comprovante'])) {
                                        $comprovantes_pagamentos = json_decode($financeiro['comprovante'], true);
                                    } else {
                                        $comprovantes_pagamentos = []; // Defina um array vazio ou um valor padrão
                                    }
                            ?>

                                    <script>
                                        function excluirDocumento(caminho) {
                                            if (confirm('Tem certeza que deseja excluir este documento?')) {
                                                var form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = 'excluir_documento.php';

                                                var inputCaminho = document.createElement('input');
                                                inputCaminho.type = 'hidden';
                                                inputCaminho.name = 'caminho_documento';
                                                inputCaminho.value = caminho;

                                                var inputIdDocumento = document.createElement('input');
                                                inputIdDocumento.type = 'hidden';
                                                inputIdDocumento.name = 'id_financeiro';
                                                inputIdDocumento.value = '<?= $financeiro['cod_financeiro'] ?>';

                                                var inputDocumentosAtuais = document.createElement('input');
                                                inputDocumentosAtuais.type = 'hidden';
                                                inputDocumentosAtuais.name = 'documentos_atuais';
                                                inputDocumentosAtuais.value = '<?= json_encode($documentos_pagamentos) ?>';

                                                form.appendChild(inputCaminho);
                                                form.appendChild(inputIdDocumento);
                                                form.appendChild(inputDocumentosAtuais);
                                                document.body.appendChild(form);
                                                form.submit();
                                            }
                                        }

                                        function excluirComprovante(caminho) {
                                            if (confirm('Tem certeza que deseja excluir este comprovante?')) {
                                                var form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = 'excluir_comprovante.php';

                                                var inputCaminho = document.createElement('input');
                                                inputCaminho.type = 'hidden';
                                                inputCaminho.name = 'caminho_comprovante';
                                                inputCaminho.value = caminho;

                                                var inputIdComprovante = document.createElement('input');
                                                inputIdComprovante.type = 'hidden';
                                                inputIdComprovante.name = 'id_financeiro';
                                                inputIdComprovante.value = '<?= $financeiro['cod_financeiro'] ?>';

                                                var inputComprovanetesAtuais = document.createElement('input');
                                                inputComprovanetesAtuais.type = 'hidden';
                                                inputComprovanetesAtuais.name = 'comprovantes_atuais';
                                                inputComprovanetesAtuais.value = '<?= json_encode($comprovantes_pagamentos) ?>';

                                                form.appendChild(inputCaminho);
                                                form.appendChild(inputIdComprovante);
                                                form.appendChild(inputComprovanetesAtuais);
                                                document.body.appendChild(form);
                                                form.submit();
                                            }
                                        }
                                    </script>

                                    <script>
                                        document.getElementById('scan-button').addEventListener('click', function() {
                                            // Configurar e iniciar o processo de escaneamento
                                            TWAIN.WebCapture.scan({
                                                onSuccess: function(scannedFiles) {
                                                    // Adicionar os arquivos escaneados ao campo de upload
                                                    const input = document.getElementById('documentos');
                                                    scannedFiles.forEach(file => {
                                                        const dataTransfer = new DataTransfer();
                                                        dataTransfer.items.add(file);
                                                        input.files = dataTransfer.files;
                                                    });
                                                    previewDocuments({
                                                        target: input
                                                    });
                                                },
                                                onError: function(error) {
                                                    console.error('Erro ao escanear documento:', error);
                                                }
                                            });
                                        });

                                        function previewDocuments(event) {
                                            // Função para pré-visualizar os documentos
                                            const container = document.getElementById('document-preview-container');
                                            container.innerHTML = '';
                                            Array.from(event.target.files).forEach(file => {
                                                const link = document.createElement('a');
                                                link.href = URL.createObjectURL(file);
                                                link.target = '_blank';
                                                link.textContent = file.name;
                                                container.appendChild(link);
                                                container.appendChild(document.createElement('br'));
                                            });
                                        }
                                    </script>                                    
                                    
                                    
                                    <label for="responsavel" class="form-label" style="color: red;">CÓDIGO DO PAGAMENTO:
                                    <?= $financeiro['cod_financeiro'] ?></label>




                                    <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="addCurrencyPrefix();">


                                        <input type="hidden" name="id" value="<?= $financeiro['cod_financeiro'] ?>">
                                        <input type="hidden" name="documentos_atuais" value='<?php echo json_encode($documentos_pagamentos) ?>'>
                                        <input type="hidden" name="comprovantes_atuais" value='<?php echo json_encode($comprovantes_pagamentos) ?>'>


                                        <div class="form-container">
                                            <div class="form-group">

                                                <label for="data" class="form-label">DATA:</label>
                                                <input type="date" name="data" required value="<?= $financeiro['data'] ?>" class="form-control">


                                                <label for="responsavel" class="form-label">RESPONSÁVEL:</label>
                                                <select name="responsavel" class="form-control" required>
                                                    <option value="" disabled selected>Selecione o Responsável</option>
                                                    <option value="MAIARA CARLA HENRIQUE PEREIRA" <?= $financeiro['responsavel'] == 'MAIARA CARLA HENRIQUE PEREIRA' ? 'selected' : '' ?>>MAIARA CARLA HENRIQUE PEREIRA</option>
                                                    <option value="CARLA MARAISA HENRIQUE PEREIRA" <?= $financeiro['responsavel'] == 'CARLA MARAISA HENRIQUE PEREIRA' ? 'selected' : '' ?>>CARLA MARAISA HENRIQUE PEREIRA</option>
                                                    <option value="M2 SHOWS PRODUÇÕES" <?= $financeiro['responsavel'] == 'M2 SHOWS PRODUÇÕES' ? 'selected' : '' ?>>M2 SHOWS PRODUÇÕES</option>
                                                </select>

                                                <label class="form-label">&nbsp;DESCRIÇÃO:</label>
                                                <textarea name="descricao" class="form-control" style="width:500px; height:150px;" id="descricao"><?= htmlspecialchars($descricao) ?></textarea>

                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">

                                                    <label for="tipo" class="form-label">TIPO:</label>
                                                    <select name="tipo" class="form-control" required>
                                                        <option value="" disabled selected>Selecione o tipo</option>
                                                        <option value="DÉBITO" <?= $financeiro['tipo'] == 'DÉBITO' ? 'selected' : '' ?>>DÉBITO</option>
                                                        <option value="CRÉDITO" <?= $financeiro['tipo'] == 'CRÉDITO' ? 'selected' : '' ?>>CRÉDITO</option>
                                                    </select>

                                                    <label class="form-label">&nbsp;VALOR:</label>
                                                    <input id="valor" type="text" name="valor" required value="<?= $financeiro['valor'] ?>" class="form-control" oninput="formatarValor(this)">

                                                    <label class="form-label">&nbsp;FORMA DE PAGAMENTO:</label>
                                                    <textarea name="forma_pagamento" class="form-control" style="width:500px; height:150px;" id="descricao"><?= htmlspecialchars($forma_pagamento) ?></textarea>


                                                </div>
                                            </div>
                                        </div>


                                        <label class="form-label">&nbsp;PROCESSO:</label>
                                        <input id="documentos" type="file" name="documentos_pagamentos[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                        <br>
                                        

                                        <div id="document-preview-container">
                                            <div class="form-group">
                                            <!-- <button id="scan-button" class="btn btn-primary">Scan Document</button> -->


                                                <?php
                                                // Supondo que $documentos_pagamentos contenha os caminhos dos documentos atuais
                                                if (!empty($documentos_pagamentos) && is_array($documentos_pagamentos)) {
                                                    foreach ($documentos_pagamentos as $documento_atual) {
                                                        echo '<div style="margin-bottom: 10px;">';
                                                        echo '<a class="documento-link" href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                        // echo ' <button class="btn btn-danger documento-botao" type="button" onclick="excluirDocumento(\'' . $documento_atual . '\')">Excluir</button>';
                                                        echo '</div>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <br>

                                        <br>
                                        <label class="form-label">&nbsp;COMPROVANTES:</label>
                                        <input id="comprovantes" type="file" name="comprovantes_pagamentos[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments1(event)">
                                        <br>

                                        <div id="document-preview-container1">




                                            <?php
                                            // Supondo que $comprovantes_pagamentos contenha os caminhos dos comprovantes atuais
                                            if (!empty($comprovantes_pagamentos) && is_array($comprovantes_pagamentos)) {
                                                foreach ($comprovantes_pagamentos as $comprovante_atual) {
                                                    echo '<div style="margin-bottom: 10px;">';
                                                    echo '<a class="documento-link" href="' . $comprovante_atual . '" target="_blank">' . basename($comprovante_atual) . '</a>';
                                                    // echo ' <button class="btn btn-danger documento-botao" type="button" onclick="excluirComprovante(\'' . $comprovante_atual . '\')">Excluir</button>';
                                                    echo '</div>';
                                                }
                                            }
                                            ?>



                                        </div>

                                        <br>
                                        <div>
                                          <!--   <button type="submit" name="edit_pagamentos" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button> -->
                                        </div>
                                <?php
                                } else {
                                    echo "<h5>Imóvel não Encontrado</h5>";
                                }
                            }
                                ?>

                                    </form>
                                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
    <script>
        $(document).ready(function() {

            $('#renavan').mask('0000000000000');
            $('#placa').mask('AAA-9A99');
            $('#uf').mask('AA');
            $('#cep').mask('00.000-000');

        });

        function convertToUppercase() {
            var inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(function(input) {
                input.value = input.value.toUpperCase();
            });
        }

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
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

        function previewDocuments1(event) {
            var files = event.target.files;
            var container = document.getElementById('document-preview-container1');
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