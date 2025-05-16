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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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


        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>

    <title>EDITAR VEÍCULOS</title>

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
                            <h4>EDITAR VEÍCULOS
                                <a href="lista_veiculos.php" class="btn btn-danger float-end"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['id']))
                                $veiculo = mysqli_real_escape_string($conn, $_GET['id']);
                            $sql = "SELECT * FROM veiculos WHERE cod_veiculo='$veiculo'";
                            $query = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($query) > 0)
                                $veiculo = mysqli_fetch_array($query);
                            $proprietario = str_replace('<br>', "\n", $veiculo['proprietario_veiculo']);
                            $foto_veiculo = $veiculo['foto_veiculo'];

                            if (isset($veiculo['documentos_veiculo'])) {
                                $documentos_veiculos = json_decode($veiculo['documentos_veiculo'], true);
                            } else {
                                $documentos_veiculos = []; // Defina um array vazio ou um valor padrão
                            }
                            ?>
                            <script>
                                function excluirDocumentoVeiculo(caminho) {
                                    if (confirm('Tem certeza que deseja excluir este documento?')) {
                                        var form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = 'excluir_documento_veiculo.php';

                                        var inputCaminho = document.createElement('input');
                                        inputCaminho.type = 'hidden';
                                        inputCaminho.name = 'caminho_documento';
                                        inputCaminho.value = caminho;

                                        var inputIdVeiculo = document.createElement('input');
                                        inputIdVeiculo.type = 'hidden';
                                        inputIdVeiculo.name = 'id_veiculo';
                                        inputIdVeiculo.value = '<?= $veiculo['cod_veiculo'] ?>';

                                        var inputDocumentosAtuais = document.createElement('input');
                                        inputDocumentosAtuais.type = 'hidden';
                                        inputDocumentosAtuais.name = 'documentos_atuais';
                                        inputDocumentosAtuais.value = '<?= json_encode($documentos_veiculos) ?>';

                                        form.appendChild(inputCaminho);
                                        form.appendChild(inputIdVeiculo);
                                        form.appendChild(inputDocumentosAtuais);
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                }
                            </script>

                            <form action="cadastrar.php" method="post" enctype="multipart/form-data">

                                <input type="hidden" name="id" value="<?= $veiculo['cod_veiculo'] ?>">
                                <input type="hidden" name="foto_atual" value="<?= $foto_veiculo ?>">
                                <input type="hidden" name="documentos_atuais" value='<?php echo json_encode($documentos_veiculos) ?>'>

                                <div class="form-container">
                                    <div id="preview-container">
                                        <img id="preview" src="<?= $foto_veiculo ?>" alt="Pré-visualização da Foto" style="display: block;">
                                    </div>

                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">NOME:</label>
                                            <input type="text" name="nome_veiculo" value="<?= $veiculo['nome_veiculo'] ?>" class="form-control" style="width:300px;" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;MARCA/MODELO:</label>
                                            <input type="text" name="marca_modelo_veiculo" value="<?= $veiculo['marca_modelo_veiculo'] ?>" class="form-control" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;FOTO:</label>
                                            <input id="foto" type="file" name="foto_veiculo" class="form-control" accept="image/*" onchange="previewImage(event)" style="width:300px;">
                                        </div>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;PLACA:</label>
                                                <input id="placa" type="text" name="placa_veiculo" value="<?= $veiculo['placa_veiculo'] ?>" class="form-control" onchange="convertToUppercase()">

                                                <label class="form-label">&nbsp;UF:</label>
                                                <input id="uf" type="text" name="uf_veiculo" value="<?= $veiculo['uf_veiculo'] ?>" class="form-control" onchange="convertToUppercase()">
                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">
                                                    <label class="form-label">RENAVAN:</label>
                                                    <input id="renavan" type="text" name="renavan_veiculo" value="<?= $veiculo['renavan_veiculo'] ?>" class="form-control" style="width:400px;">

                                                    <label class="form-label">CHASSI:</label>
                                                    <input type="text" name="chassi_veiculo" class="form-control" value="<?= $veiculo['chassi_veiculo'] ?>" onchange="convertToUppercase()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label">&nbsp;PROPRIETÁRIO:</label>


                                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                    <textarea name="proprietario_veiculo" class="form-control" style="height:150px;"><?= htmlspecialchars($proprietario) ?></textarea>
                                </div>
                                <label class="form-label">&nbsp;DOCUMENTOS:</label>


                                <input id="documentos" type="file" name="documentos_veiculo[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                <br>

                                <div id="document-preview-container">
                                    <div class="form-group">
                                        <?php
                                        if (!empty($documentos_veiculos) && is_array($documentos_veiculos)) {
                                            foreach ($documentos_veiculos as $documento_atual) {
                                                echo '<div style="margin-bottom: 10px;">';
                                                echo '<a class="documento-link" href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <br>

                                <div>
                                    <button type="submit" name="edit_veiculos" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button>
                                </div>
                            </form>

                            <script>
                                $(document).ready(function() {
                                    $('#renavan').mask('0000000000000');
                                    $('#placa').mask('AAA-9A99');
                                    $('#uf').mask('AA');
                                });

                                function convertToUppercase() {
                                    var inputs = document.querySelectorAll('input[type="text"], textarea');
                                    inputs.forEach(function(input) {
                                        input.value = input.value.toUpperCase();
                                    });
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