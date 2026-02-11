<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <title>CADASTRO DE VEÍCULOS</title>
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <style>
        .form-container { display: flex; flex-direction: row; gap: 20px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 10px; }
        .form-label { font-weight: bold; margin-bottom: 5px; }
        input[type="text"], textarea, select, input[type="file"] { width: 100%; }
        #preview-container { width: 200px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 10px; }
        #preview { width: 100%; height: 100%; object-fit: contain; display: none; }
        #document-preview-container { display: flex; flex-direction: row; gap: 10px; }
        iframe { width: 100%; height: 200px; border: 1px solid #ddd; }
        .table-container { width: 100%; overflow-x: auto; }
        
        /* Estilo do Editor Quill */
        #editor_proprietario { height: 200px; background-color: white; border: 1px solid #ccc; }
    </style>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">CADASTRO DE VEÍCULOS
                            <a href="lista_veiculos.php" class="btn btn-danger float-end btn-sm"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="cadastrar.php" method="post" id="formCadastro" enctype="multipart/form-data">
                            <div class="form-container">
                                <div id="preview-container">
                                    <img id="preview" src="#" alt="Pré-visualização da Foto">
                                </div>

                                <div class="flex-fill">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">NOME:</label>
                                            <input type="text" name="nome_veiculo" class="form-control" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label">PLACA:</label>
                                            <input id="placa" type="text" name="placa_veiculo" class="form-control">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label">RENAVAN:</label>
                                            <input id="renavan" type="text" name="renavan_veiculo" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">MARCA/MODELO:</label>
                                            <input type="text" name="marca_modelo_veiculo" class="form-control" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label class="form-label">UF:</label>
                                            <input id="uf" type="text" name="uf_veiculo" class="form-control">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="form-label">CHASSI:</label>
                                            <input type="text" name="chassi_veiculo" class="form-control" onkeyup="this.value = this.value.toUpperCase()">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">FOTO:</label>
                                        <input id="foto" type="file" name="foto_veiculo" class="form-control" accept="image/*" onchange="previewImage(event)">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">DADOS DO PROPRIETÁRIO:</label>
                                <div id="editor_proprietario"></div>
                                <textarea name="propietario_veiculo" id="textarea_proprietario" style="display:none;"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">DOCUMENTOS:</label>
                                <input id="documentos" type="file" name="documentos_veiculos[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                <div id="document-preview-container" class="mt-2"></div>
                            </div>

                            <button type="submit" name="cad_veiculos" class="btn btn-success w-100 btn-lg">
                                <span class="bi-file-earmark-plus-fill"></span>&nbsp;CADASTRAR VEÍCULO
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var quill;

        $(document).ready(function() {
            // Máscaras
            $('#renavan').mask('00000000000');
            $('#placa').mask('AAA-9A99');
            $('#uf').mask('AA');

            // Inicializa Quill
            quill = new Quill('#editor_proprietario', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        ['clean']
                    ]
                }
            });
        });

        // Sincroniza Quill com o campo oculto antes do submit
        document.getElementById('formCadastro').onsubmit = function() {
            var html = quill.root.innerHTML;
            document.getElementById('textarea_proprietario').value = (html === '<p><br></p>') ? '' : html;
        };

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
                var url = URL.createObjectURL(files[i]);
                var iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.style.width = '200px';
                iframe.style.height = '150px';
                iframe.className = 'me-2 border';
                container.appendChild(iframe);
            }
        }
    </script>
</body>
</html>