<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <title>CADASTRO DE CONTRATOS</title>
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
                            <h4>CADASTRO DE CONTRATOS
                                <a href="lista_contratos.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="convertToUppercase(); addCurrencyPrefix();">
                                <div class="form-container">



                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">NOME:</label>
                                            <input type="text" name="nome" class="form-control" style="width:450px;" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;TIPO DE CONTRATO:</label>
                                            <select name="tipo_contrato" class="form-control" required>
                                                <option value="" disabled selected>Selecione o Tipo de Contrato</option>
                                                <option value="ALUGUEL">ALUGUEL</option>
                                                <option value="CONTA GARANTIDA">CONTA GARANTIDA</option>
                                                <option value="CRÉDITO RURAL">CRÉDITO RURAL</option>
                                                <option value="EMPRÉSTIMO PESSOAL">EMPRÉSTIMO PESSOAL</option>
                                                <option value="PLANO DE SAÚDE">PLANO DE SAÚDE</option>
                                                <option value="PRESTAÇÃO DE SERVIÇO">PRESTAÇÃO DE SERVIÇO</option>
                                                <option value="SEGURO AUTO">SEGURO AUTO</option>
                                                <option value="SEGURO DE VIDA">SEGURO DE VIDA</option>
                                                <option value="SEGURO EMPRESARIAL">SEGURO EMPRESARIAL</option>
                                                <option value="SEGURO RESIDENCIAL">SEGURO RESIDENCIAL</option>
                                            </select>


                                        </div>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;DATA DE INÍCIO:</label>
                                                <input type="date" name="data_inicial" required class="form-control">

                                                <label class="form-label">&nbsp;DATA DE TÉRMINIO:</label>
                                                <input type="date" name="data_final" required class="form-control">
                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">
                                                    <label class="form-label" style="width:400px;">SITUAÇÃO ATUAL:</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="" disabled selected>Selecione a Situação</option>
                                                        <option value="ATIVO">ATIVO</option>
                                                        <option value="CANCELADO">CANCELADO</option>
                                                        <option value="FINALIZADO">FINALIZADO</option>
                                                        
                                                    </select>

                                                    <label class="form-label">&nbsp;VALOR:</label>
                                                    <input id="valor" type="text" name="valor" required class="form-control" oninput="formatarValor(this)">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label">&nbsp;DESCRIÇÃO:</label>
                                <textarea name="descricao" class="form-control" style="height:150px;" onchange="convertToUppercase()"></textarea>


                                <label class="form-label">&nbsp;ANEXO:</label>
                                <input id="anexo" type="file" name="anexo[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                <br>
                                <div id="document-preview-container"></div>
                                <br>
                                <div>
                                    <button type="submit" name="cad_contratos" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
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

        function addCurrencyPrefix() {
            var valorInput = document.getElementById('valor');
            if (valorInput.value && !valorInput.value.startsWith('R$')) {
                valorInput.value = 'R$ ' + valorInput.value;
            }
        }
    </script>
    <!--

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
    -->
</body>

</html>