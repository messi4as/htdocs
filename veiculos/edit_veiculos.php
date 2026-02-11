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
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>

    <style>
        .form-container { display: flex; flex-direction: row; gap: 20px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 10px; }
        .form-label { font-weight: bold; margin-bottom: 5px; }
        #preview-container { width: 200px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9; }
        #preview { width: 100%; height: 100%; object-fit: contain; }
        #editor_proprietario { height: 200px; background-color: white; border: 1px solid #ccc; }
        .documento-item { display: flex; align-items: center; background: #f8f9fa; padding: 8px; border: 1px solid #eee; border-radius: 4px; margin-bottom: 5px; }
        .documento-link { flex-grow: 1; text-decoration: none; color: #007bff; font-weight: 500; }
    </style>

    <title>EDITAR VEÍCULO</title>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <?php include('/xampp/htdocs/mensagem.php'); ?>
        
        <?php
        if (isset($_GET['id'])) {
            $veiculo_id = mysqli_real_escape_string($conn, $_GET['id']);
            $sql = "SELECT * FROM veiculos WHERE cod_veiculo='$veiculo_id'";
            $query = mysqli_query($conn, $sql);
            if (mysqli_num_rows($query) > 0) {
                $veiculo = mysqli_fetch_array($query);
                $proprietario_conteudo = stripslashes($veiculo['proprietario_veiculo']);
                $foto_veiculo = $veiculo['foto_veiculo'];
                $docs_json = $veiculo['documentos_veiculo'];
                $documentos = json_decode($docs_json, true) ?: [];
            }
        }
        ?>

        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <h4 class="mb-0">EDITAR VEÍCULO</h4>
                <a href="lista_veiculos.php" class="btn btn-danger btn-sm">VOLTAR</a>
            </div>
            <div class="card-body">
                <form action="cadastrar.php" method="post" id="veForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $veiculo['cod_veiculo'] ?>">
                    <input type="hidden" name="foto_atual" value="<?= $foto_veiculo ?>">
                    <input type="hidden" name="documentos_atuais" value='<?= $docs_json ?>'>

                    <div class="form-container mb-3">
                        <div id="preview-container">
                            <img id="preview" src="<?= $foto_veiculo ?>" style="<?= $foto_veiculo ? '' : 'display:none;' ?>">
                        </div>
                        <div class="flex-fill">
                            <label class="form-label">NOME:</label>
                            <input type="text" name="nome_veiculo" value="<?= $veiculo['nome_veiculo'] ?>" class="form-control mb-2" onkeyup="this.value = this.value.toUpperCase()">
                            <label class="form-label">MARCA/MODELO:</label>
                            <input type="text" name="marca_modelo_veiculo" value="<?= $veiculo['marca_modelo_veiculo'] ?>" class="form-control mb-2" onkeyup="this.value = this.value.toUpperCase()">
                            <label class="form-label">FOTO:</label>
                            <input type="file" name="foto_veiculo" class="form-control" onchange="previewImage(event)">
                        </div>
                        <div style="width: 200px;">
                            <label class="form-label">PLACA:</label>
                            <input id="placa" type="text" name="placa_veiculo" value="<?= $veiculo['placa_veiculo'] ?>" class="form-control mb-2">
                            <label class="form-label">UF:</label>
                            <input id="uf" type="text" name="uf_veiculo" value="<?= $veiculo['uf_veiculo'] ?>" class="form-control mb-2">
                            <label class="form-label">RENAVAN:</label>
                            <input id="renavan" type="text" name="renavan_veiculo" value="<?= $veiculo['renavan_veiculo'] ?>" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">DADOS DO PROPRIETÁRIO:</label>
                        <div id="editor_proprietario"></div>
                        <textarea name="proprietario_veiculo" id="textarea_proprietario" style="display:none;"><?= $proprietario_conteudo ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-primary fw-bold">ANEXAR NOVOS DOCUMENTOS:</label>
                        <input type="file" name="documentos_veiculo[]" class="form-control" multiple onchange="gerarInputsNomes(event)">
                        <div id="nomes-documentos-container" class="mt-2"></div>
                    </div>

                    <div class="mb-3 border p-2">
                        <label class="form-label">DOCUMENTOS ATUAIS:</label>
                        <div id="lista_docs">
                            <?php foreach ($documentos as $doc): 
                                $path = is_array($doc) ? $doc['path'] : $doc;
                                $label = is_array($doc) ? $doc['nome'] : basename($doc);
                            ?>
                                <div class="documento-item">
                                    <a href="<?= $path ?>" target="_blank" class="documento-link">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i> <?= $label ?>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="excluirDocumento('<?= addslashes($path) ?>')">
                                        <i class="bi bi-trash"></i> Excluir
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" name="edit_veiculos" class="btn btn-success w-100 btn-lg">SALVAR ALTERAÇÕES</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        var quill;

        $(document).ready(function() {
            $('#placa').inputmask('AAA-9A99');
            $('#uf').inputmask('AA');
            $('#renavan').inputmask('99999999999');
        });

        window.onload = function() {
            quill = new Quill('#editor_proprietario', {
                theme: 'snow',
                modules: {
                    toolbar: [['bold', 'italic', 'underline'], [{'list': 'ordered'}, {'list': 'bullet'}], ['clean']]
                }
            });

            var dadosIniciais = document.getElementById('textarea_proprietario').value;
            if (dadosIniciais) {
                setTimeout(function() {
                    quill.clipboard.dangerouslyPasteHTML(dadosIniciais);
                }, 150);
            }
        };

        document.getElementById('veForm').onsubmit = function() {
            document.getElementById('textarea_proprietario').value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
        };

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result; output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function gerarInputsNomes(event) {
            const container = document.getElementById('nomes-documentos-container');
            container.innerHTML = '';
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const div = document.createElement('div');
                div.className = 'input-group mb-1 shadow-sm';
                div.innerHTML = `
                    <span class="input-group-text small" style="font-size:10px; width: 150px; overflow: hidden;">${files[i].name}</span>
                    <input type="text" name="nomes_arquivos[]" class="form-control form-control-sm" placeholder="Dê um nome a este documento" required>
                `;
                container.appendChild(div);
            }
        }

        // FUNÇÃO DE EXCLUSÃO
        function excluirDocumento(caminho) {
            if (confirm('Tem certeza que deseja excluir este documento permanentemente?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'excluir_documento_veiculo.php';

                form.innerHTML = `
                    <input type="hidden" name="caminho_documento" value="${caminho}">
                    <input type="hidden" name="id_veiculo" value="<?= $veiculo['cod_veiculo'] ?>">
                    <input type="hidden" name="documentos_atuais" value='<?= $docs_json ?>'>
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>