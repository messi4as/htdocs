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
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery deve ser carregado primeiro -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Plugins que dependem do jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    <!-- Bootstrap JS -->
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>


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

    <title>EDITAR IMÓVEIS</title>

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

                            <h4>EDITAR IMÓVEIS
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>

                        <div class="card-body">

                            <?php
                            if (isset($_GET['id'])) {
                                $id_imovel = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM imoveis WHERE cod_imovel='$id_imovel'";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $imovel = mysqli_fetch_array($query);
                                    $proprietario = str_replace('<br>', "\n", $imovel['proprietario_imovel']);
                                    $inscricao = str_replace('<br>', "\n", $imovel['inscricao_imovel']);
                                    $condominio = str_replace('<br>', "\n", $imovel['condominio_imovel']);
                                    $tv = str_replace('<br>', "\n", $imovel['tv_imovel']);
                                    $energia = str_replace('<br>', "\n", $imovel['energia_imovel']);
                                    $agua = str_replace('<br>', "\n", $imovel['agua_imovel']);
                                    $gas = str_replace('<br>', "\n", $imovel['gas_imovel']);
                                    $internet = str_replace('<br>', "\n", $imovel['internet_imovel']);


                                    if (isset($imovel['documentos_imovel'])) {
                                        $documentos_imoveis = json_decode($imovel['documentos_imovel'], true);
                                    } else {
                                        $documentos_imoveis = []; // Defina um array vazio ou um valor padrão
                                    }

                            ?>

                                    <script>
                                        function excluirDocumentoImovel(caminho) {
                                            if (confirm('Tem certeza que deseja excluir este documento?')) {
                                                var form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = 'excluir_documento_imovel.php';

                                                var inputCaminho = document.createElement('input');
                                                inputCaminho.type = 'hidden';
                                                inputCaminho.name = 'caminho_documento';
                                                inputCaminho.value = caminho;

                                                var inputIdImovel = document.createElement('input');
                                                inputIdImovel.type = 'hidden';
                                                inputIdImovel.name = 'id_imovel';
                                                inputIdImovel.value = '<?= $imovel['cod_imovel'] ?>';

                                                var inputDocumentosAtuais = document.createElement('input');
                                                inputDocumentosAtuais.type = 'hidden';
                                                inputDocumentosAtuais.name = 'documentos_atuais';
                                                inputDocumentosAtuais.value = '<?= json_encode($documentos_imoveis) ?>';

                                                form.appendChild(inputCaminho);
                                                form.appendChild(inputIdImovel);
                                                form.appendChild(inputDocumentosAtuais);
                                                document.body.appendChild(form);
                                                form.submit();
                                            }
                                        }
                                    </script>





                                    <form action="cadastrar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $imovel['cod_imovel'] ?>">
                                        <input type="hidden" name="documentos_atuais" value='<?php echo json_encode($documentos_imoveis); ?>'>


                                        <div class="form-container">
                                            <div class="form-group">

                                                <label class="form-label">NOME:</label>
                                                <input type="text" name="nome_imovel" value="<?= $imovel['nome_imovel'] ?>" class="form-control" onchange="convertToUppercase()" style="width:500px;">

                                                <label class="form-label">&nbsp;ENDEREÇO:</label>
                                                <input type="text" name="endereco_imovel" value="<?= $imovel['endereco_imovel'] ?>" class="form-control" onchange="convertToUppercase()">

                                                <label class="form-label">&nbsp;BAIRRO:</label>
                                                <input type="text" name="bairro_imovel" value="<?= $imovel['bairro_imovel'] ?>" class="form-control" onchange="convertToUppercase()" style="width:500px;">

                                            </div>
                                            <br>


                                            <div class="form-container">
                                                <div class="form-group">




                                                    <label class="form-label">&nbsp;CEP:</label>
                                                    <input id="cep" type="text" name="cep_imovel" value="<?= $imovel['cep_imovel'] ?>" class="form-control">



                                                    <label class="form-label">&nbsp;LOCALIZAÇÃO:</label>
                                                    <input type="text" name="localizacao_imovel" value="<?= $imovel['localizacao_imovel'] ?>" class="form-control" style="width:500px;">
                                                </div>
                                            </div>
                                        </div>
                                        <br>



                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;PROPRIETÁRIO:</label>
                                                <textarea name="proprietario_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($proprietario) ?></textarea>

                                                <label class="form-label">&nbsp;ENERGIA:</label>
                                                <textarea name="energia_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($energia) ?></textarea>
                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">

                                                    <label class="form-label">&nbsp;INSCRIÇÃO:</label>
                                                    <textarea name="inscricao_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($inscricao) ?></textarea>

                                                    <label class="form-label">&nbsp;ÁGUA:</label>
                                                    <textarea name="agua_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($agua) ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-container">
                                            <div class="form-group">

                                                <label class="form-label">&nbsp;CONDOMÍNIO:</label>
                                                <textarea name="condominio_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($condominio) ?></textarea>

                                                <label class="form-label">&nbsp;TV POR ASSINATURA:</label>
                                                <textarea name="tv_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($tv) ?></textarea>

                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">


                                                    <label class="form-label">&nbsp;GÁS:</label>
                                                    <textarea name="gas_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($gas) ?></textarea>

                                                    <label class="form-label">&nbsp;INTERNET:</label>
                                                    <textarea name="internet_imovel" class="form-control" style="width:500px; height:150px;"><?= htmlspecialchars($internet) ?></textarea>

                                                </div>
                                            </div>
                                        </div>



                                        <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                        <input id="documentos" type="file" name="documentos_imovel[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                        <br>

                                        <div id="document-preview-container">
                                            <div class="form-group">


                                                <?php
                                                // Supondo que $documentos_imoveis contenha os caminhos dos documentos atuais
                                                if (!empty($documentos_imoveis) && is_array($documentos_imoveis)) {
                                                    foreach ($documentos_imoveis as $documento_atual) {
                                                        echo '<div style="margin-bottom: 10px;">';
                                                        echo '<a class="documento-link" href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                       // echo ' <button class="btn btn-danger documento-botao" type="button" onclick="excluirDocumentoImovel(\'' . $documento_atual . '\')">Excluir</button>';
                                                        echo '</div>';
                                                    }
                                                }
                                                ?>


                                            </div>
                                        </div>
                                        <br>

                                        <div>
                                            <button type="submit" name="edit_imoveis" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button>
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