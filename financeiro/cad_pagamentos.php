<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <title>CADASTRO DE PAGAMENTOS</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
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
    </style>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>CADASTRO DE PAGAMENTOS
                            <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="addCurrencyPrefix();">

                            <div class="form-container">
                                <div class="form-group">

                                    <label for="data" class="form-label">DATA:</label>
                                    <input type="date" name="data" class="form-control" required>


                                    <label for="responsavel" class="form-label">RESPONSÁVEL:</label>
                                    <select name="responsavel" class="form-control" required>
                                        <option value="" disabled selected>Selecione o Responsável</option>
                                        <option value="MAIARA CARLA HENRIQUE PEREIRA">MAIARA CARLA HENRIQUE PEREIRA</option>
                                        <option value="CARLA MARAISA HENRIQUE PEREIRA">CARLA MARAISA HENRIQUE PEREIRA</option>
                                        <option value="M2 SHOWS PRODUÇÕES">M2 SHOWS PRODUÇÕES</option>
                                    </select>

                                    <label class="form-label">&nbsp;DESCRIÇÃO:</label>
                                    <textarea name="descricao" class="form-control" style="width:500px; height:150px;"></textarea>

                                </div>

                                <div class="form-container">
                                    <div class="form-group">

                                        <label for="tipo" class="form-label">TIPO:</label>
                                        <select name="tipo" class="form-control" required>
                                            <option value="" disabled selected>Selecione o tipo</option>
                                            <option value="DÉBITO">DÉBITO</option>
                                            <option value="CRÉDITO">CRÉDITO</option>
                                        </select>

                                        <label class="form-label">&nbsp;VALOR:</label>
                                        <input id="valor" type="text" name="valor" required class="form-control" oninput="formatarValor(this)">

                                        <label class="form-label">&nbsp;FORMA DE PAGAMENTO:</label>
                                        <textarea name="forma_pagamento" class="form-control" style="width:500px; height:150px;"></textarea>


                                    </div>
                                </div>
                            </div>
                            <label class="form-label">&nbsp;PROCESSO:</label>
                            <input id="documentos" type="file" name="documentos_pagamentos[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                            <br>
                            <div id="document-preview-container"></div>
                            <br>
                            <label class="form-label">&nbsp;COMPROVANTES:</label>
                            <input id="comprovantes" type="file" name="comprovantes_pagamentos[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments1(event)">
                            <br>
                            <div id="document-preview-container1"></div>
                            <br>
                            <div>
                                <button type="submit" name="cad_pagamentos" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/jquery.mask.min.js"></script>
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