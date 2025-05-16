<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <title>CADASTRO DE ITENS</title>
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
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
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>CADASTRO DE ITENS
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="convertToUppercase(); addCurrencyPrefix();">
                                <div class="form-container">

                                    <div id="preview-container">
                                        <img id="preview" src="#" alt="Pré-visualização da Foto">
                                    </div>

                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">NOME:</label>
                                            <input type="text" name="nome_item" class="form-control" style="width:300px;" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;CATEGORIA:</label>
                                            <input type="text" name="categoria" class="form-control" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;FOTO:</label>
                                            <input id="foto" type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)" style="width:300px;">
                                        </div>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;QUANTIDADE:</label>
                                                <input id="quantidade" type="text" name="quantidade" class="form-control" onchange="convertToUppercase()">

                                                <label class="form-label">&nbsp;VALOR:</label>
                                                <input id="valor" type="text" name="valor" class="form-control" onchange="convertToUppercase()">

                                                <label class="form-label">&nbsp;STATUS:</label>
                                                <input id="status" type="text" name="status" class="form-control" onchange="convertToUppercase()">
                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">
                                                    <label class="form-label">ORIGEM:</label>
                                                    <input id="origem" type="text" name="origem" class="form-control" style="width:400px;">

                                                    <label class="form-label">LOCAL:</label>
                                                    <select name="local" class="form-control" required>
                                                        <option value="" disabled selected>Selecione o local</option>
                                                        <option value="TÉRREO - SALA 01">TÉRREO - SALA 01</option>
                                                        <option value="CAMARIM 01">CAMARIM 01</option>
                                                        <option value="CAMARIM 02">CAMARIM 02</option>
                                                        <option value="BOX 01">BOX 01</option>
                                                        <option value="BOX 02">BOX 02</option>
                                                        <option value="BOX 03">BOX 03</option>
                                                        <option value="BOX 04">BOX 04</option>
                                                        <option value="BOX 05">BOX 05</option>
                                                        <option value="BOX 06">BOX 06</option>
                                                        <option value="BOX 07">BOX 07</option>
                                                        <option value="BOX 08">BOX 08</option>
                                                        <option value="BOX 09">BOX 09</option>
                                                        <option value="BOX 10">BOX 10</option>
                                                        <option value="BOX 11">BOX 11</option>
                                                        <option value="BOX 12">BOX 12</option>
                                                        <option value="1º ANDAR - SALA 01">1º ANDAR - SALA 01</option>
                                                        <option value="1º ANDAR - SALA 02">1º ANDAR - SALA 02</option>
                                                        <option value="1º ANDAR - SALA 03">1º ANDAR - SALA 03</option>
                                                    </select>

                                                    <label for="data_entrada" class="form-label">DATA DE ENTRADA:</label>
                                                    <input type="date" name="data_entrada" class="form-control" required>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label">&nbsp;DESCRIÇÃO:</label>
                                <textarea name="descricao" class="form-control" style="height:150px;"></textarea>


                                <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                <input id="documentos" type="file" name="anexos_documentos[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                <br>
                                <div id="document-preview-container"></div>
                                <br>
                                <div>
                                    <button type="submit" name="cad_itens" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
                                </div>
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

        });

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


</body>

</html>